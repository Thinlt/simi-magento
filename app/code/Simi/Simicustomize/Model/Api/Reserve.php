<?php
namespace Simi\Simicustomize\Model\Api;

use Magento\Customer\Model\Session as CustomerSession;

class Reserve extends \Simi\Simiconnector\Model\Api\Apiabstract implements \Simi\Simicustomize\Api\ReserveInterface
{
    /**
     * \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $config;

    /**
    * @var \Magento\Framework\Stdlib\DateTime\DateTime
    */
    protected $date;

    /**
    * @var \Magento\Framework\Mail\Template\TransportBuilder
    */
    protected $transportBuilder;

    /**
    * @var \Magento\Framework\Translate\Inline\StateInterface
    */
    protected $inlineTranslation;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    public $allow_filter_core = false;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\RequestInterface $request,
        CustomerSession $customerSession
    ){
        $this->request = $request;
        $this->config = $config;
        $this->date = $date;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->customerSession = $customerSession;
        parent::__construct($simiObjectManager);
    }

    /**
     * Set builder query
     * @return boolean
     */
    public function setBuilderQuery()
    {
        $this->builderQuery = false;
    }

    /**
     * @inheritdoc
     */
    public function getMyReserved(){
        $customerId = $this->customerSession->getCustomer()->getId();
        if (isset($customerId) && $customerId) 
        {
            $model = $this->simiObjectManager->get('\Simi\Simicustomize\Model\Reserve');
            $collection = $model->getCollection();
            $this->builderQuery = $collection;
            $collection->addFieldToFilter('customer_id', $customerId);
            $parameters = $this->request->getParams();
            if ($this->request->getContent()) {
                $parameters2 = json_decode($this->request->getContent(), true);
                $parameters = array_merge_recursive($parameters, $parameters2);
            }
            $this->order($parameters);
            $page  = 1;
            $limit = self::DEFAULT_LIMIT;
            $offset = 0;
            $this->setPageSize($collection, $parameters, $limit, $offset, $page);
            if ($collection->getSize()) {
                return ['data' => $collection->toArray()];
            }
            return ['data' => ['status' => false, 'error' => __('No data!')]];
        }
        return ['data' => ['status' => false, 'error' => __('Invalid request value!')]];
    }

    /**
     * @inheritdoc
     */
    public function cancelMyReserved($id){
        if($id){
            $model = $this->simiObjectManager->get('\Simi\Simicustomize\Model\Reserve')->load($id);
            if($model->getId()){
                try{
                    $model->setStatus('Cancelled');
                    $model->save();
                    return $this->getMyReserved();
                }catch(\Exception $e){
                    return ['data' => ['status' => false, 'error' => $e->getMessage()]];
                }
            }
        }
        return ['data' => ['status' => false, 'error' => __('Invalid request value!')]];
    }

    /**
     * Save Reserve request
     * @return boolean
     */
    public function index() {
        $reserve = $this->simiObjectManager->get('\Simi\Simicustomize\Model\Reserve');
        $data = $this->request->getParams();
        if ($this->request->getContent()) {
            $data = json_decode($this->request->getContent(), true);
        }
        if (isset($data['product_id']) && isset($data['storelocator_id']) && isset($data['customer_id'])) {
            $reserve->setData('product_id', $data['product_id']);
            $reserve->setData('product_name', $data['product_name']);
            // $reserve->setData('category_name', $data['category_name']);
            $reserve->setData('storelocator_id', $data['storelocator_id']);
            $reserve->setData('store_name', $data['store_name']);
            $reserve->setData('customer_id', $data['customer_id']);
            $reserve->setData('customer_name', $data['customer_name']);
            $reserve->setData('status', 'Pending');
            $collection = $reserve->getCollection();
            $collection->addFieldToFilter('product_id', $data['product_id'])
                ->addFieldToFilter('storelocator_id', $data['storelocator_id'])
                ->addFieldToFilter('customer_id', $data['customer_id'])
                ->load();
            if ($collection->getSize()) {
                $item = $collection->getFirstItem();
                // compare with date format 'Y-m-d H:i:s'
                if ($this->date->gmtDate('Y-m-d', $item->getData('date')) == $this->date->gmtDate('Y-m-d')) {
                    $reserve = $item;
                }
            }
            if (isset($data['request_info'])) {
                if (isset($data['request_info']['product']) && isset($data['request_info']['super_attribute'])) {
                    $_product = $this->simiObjectManager->create('\Magento\Catalog\Model\Product')->load($data['request_info']['product']);
                    $optionInfos = [];
                    $attributes = $data['request_info']['super_attribute'];
                    foreach($attributes as $code => $optionId){
                        $attr = $_product->getResource()->getAttribute($code);
                        if ($attr->usesSource()) {
                            $optionText = $attr->getSource()->getOptionText($optionId);
                            if (is_array($optionText)) {
                                $optionText = implode(',', $optionText);
                            }
                            $optionInfos[] = $attr->getAttributeCode().' '.$optionText;
                        }
                    }
                    $reserve->setData('request_info', implode(', ', $optionInfos));
                }
            }
            // calculate next date for reservation date
            $reserve->setData('date', $this->date->gmtDate());
            $reserve->setData('reservation_date', $this->_getNextWorkingDay());
            try{
                $reserve->save();
                try{
                    // send email to customer
                    $customer = $this->simiObjectManager->create('\Magento\Customer\Model\Customer')->load($data['customer_id']);
                    if ($customer->getEmail()) {
                        $this->inlineTranslation->suspend();
                        $postObject = new \Magento\Framework\DataObject();
                        $postObject->setData($reserve->getData());
                        $postObject->setData('customer_email', $customer->getEmail());
                        $postObject->setData('email', $customer->getEmail());

                        //get store address from Storelocator
                        $storeLocatorId = $reserve->getData('storelocator_id');
                        $storeLocator = $this->simiObjectManager->get('\Simi\Simistorelocator\Model\Store')->load($storeLocatorId);
                        $postObject->setData('store_address', $storeLocator->getAddress());
                        // reservation date format
                        $postObject->setData('reservation_date', $this->getFormatDate($reserve->getData('reservation_date')));

                        //get first category
                        $product = $this->simiObjectManager->get('\Magento\Catalog\Model\Product')->load($reserve->getData('product_id'));
                        if ($product->getId()) {
                            $categoryIds = $product->getCategoryIds();
                            $categoryId = isset($categoryIds[0]) ? end($categoryIds) : false;
                            if ($categoryId) {
                                $categoryCollection = $this->simiObjectManager->get('\Magento\Catalog\Model\Category')->getCollection();
                                $categoryCollection->addAttributeToFilter('entity_id', $categoryId);
                                $category = $categoryCollection->getFirstItem();
                                $category->load($category->getId());
                                if ($category) {
                                    $postObject->setData('category_name', $category->getName());
                                }
                            }
                        }

                        $error = false;
                        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                        
                        $sender = [
                            'name' => $this->config->getValue('trans_email/ident_sales/name', $storeScope),
                            'email' => $this->config->getValue('trans_email/ident_sales/email', $storeScope),
                        ];
                        
                        $transport = $this->transportBuilder
                            ->setTemplateIdentifier($this->config->getValue('sales/reserve/emailTemplate', $storeScope)) // this code we have mentioned in the email_templates.xml
                            ->setTemplateOptions([
                                'area' => \Magento\Framework\App\Area::AREA_ADMINHTML, // this is using frontend area to get the template file
                                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                            ])
                            ->setTemplateVars(['data' => $postObject])
                            ->setFrom($sender)
                            ->addTo($customer->getEmail())
                            ->getTransport();
                        
                        $transport->sendMessage();
                        $this->inlineTranslation->resume();
                    }
                }catch(\Exception $e){
                    // return ['data' => ['status' => false, 'message' => $e->getMessage()]];
                }
                
                return true;
            }catch(\Exception $e){
                return ['data' => ['status' => false, 'message' => $e->getMessage()]];
            }
        }
        return ['data' => ['status' => false, 'message' => __('Invalid request value!')]];
    }

    /**
     * calculate next date for reservation date
     */
    protected function _getNextWorkingDay(){
        $submitDate = new \DateTime();
        $submitDate->modify('+1 day');
        $working_days = $this->config->getValue('sales/reserve/working_days');
        if ($working_days) {
            $working_days = str_replace(' ', '', strtolower($working_days));
            $working_days = explode(',', $working_days);
        } else {
            $working_days = [];
        }
        $holidays = $this->config->getValue('sales/reserve/holiday');
        if ($holidays) {
            $holidays = str_replace(' ', '', strtolower($holidays));
            $holidays = explode(',', $holidays);
        } else {
            $holidays = [];
        }
        $date = $this->date->gmtDate(null, $submitDate->format('Y-m-d H:i:s'));
        // next date for workday
        $workday = date('D', $this->date->gmtTimestamp($date));
        if (count($working_days)){ 
            for($i = 0; $i<8; $i++){
                if (!in_array(strtolower($workday), $working_days)) {
                    $submitDate->modify('+1 day');
                    $date = $this->date->gmtDate(null, $submitDate->format('Y-m-d H:i:s'));
                    $workday = date('D', $this->date->gmtTimestamp($date));
                }
            }
        }
        // next date for holiday
        if (count($holidays)){ 
            for($i = 0; $i<count($holidays); $i++){
                $checkDateHoliday = $submitDate->format('j/n'); //date/month without leading zeros
                if (in_array($checkDateHoliday, $holidays)) {
                    $submitDate->modify('+1 day');
                    $date = $this->date->gmtDate(null, $submitDate->format('Y-m-d H:i:s'));
                }
            }
        }
        return $date;
    }

    protected function getFormatDate($date){
        return date('F d, Y', $this->date->gmtTimestamp($date));
    }

    private function setPageSize($collection, $parameters, &$limit, &$offset, &$page)
    {
        if (isset($parameters[self::PAGE]) && $parameters[self::PAGE]) {
            $page = $parameters[self::PAGE];
        }
        if (isset($parameters[self::LIMIT]) && $parameters[self::LIMIT]) {
            $limit = $parameters[self::LIMIT];
        }
        $offset = $limit * ($page - 1);
        if (isset($parameters[self::OFFSET]) && $parameters[self::OFFSET]) {
            $offset = $parameters[self::OFFSET];
        }
        // $collection->setPageSize($offset + $limit);
        $collection->getSelect()->limit($limit, $offset);
    }

    /**
     * @return collection
     * override
     */
    public function order($params)
    {
        if ($this->builderQuery && isset($params['dir']) && isset($params['order'])) {
            $query = $this->builderQuery;
            $order = isset($params[self::ORDER]) ? $params[self::ORDER] : $this->getDefaultOrder();
            $order = str_replace('|', '.', $order);
            $dir = isset($params[self::DIR]) ? $params[self::DIR] : $this->getDefaultDir();
            $query->getSelect()->order($order.' '.$dir);
        }
        return null;
    }
}
