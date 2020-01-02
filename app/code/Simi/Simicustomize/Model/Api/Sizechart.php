<?php
namespace Simi\Simicustomize\Model\Api;

class Sizechart extends \Simi\Simiconnector\Model\Api\Apiabstract implements \Simi\Simicustomize\Api\SizechartInterface
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

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\RequestInterface $request
    ){
        $this->request = $request;
        $this->config = $config;
        $this->date = $date;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
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
     * Save Sizecharts request
     * @return boolean
     */
    public function index() {
        $model = $this->simiObjectManager->get('\Simi\Simicustomize\Model\SizeChart');
        $data = $this->request->getParams();
        if ($this->request->getContent()) {
            $data = json_decode($this->request->getContent(), true);
        }
        if (isset($data['bust']) && $data['bust'] && isset($data['waist']) && $data['waist'] 
            && isset($data['hip']) && $data['hip']) 
        {
            // check to create new or update existed item
            $collection = $model->getCollection();
            $collection->addFieldToFilter('customer_id', $data['customer_id'])
                ->addFieldToFilter('product_id', $data['product_id'])
                ->load();
            if ($collection->getSize()) {
                $item = $collection->getFirstItem();
                // compare with date format 'Y-m-d H:i:s'
                // if ($this->date->gmtDate('Y-m-d', $item->getData('created_time')) == $this->date->gmtDate('Y-m-d')) {
                //     $model = $item;
                // }
                $model = $item;
            }
            $model->setData($data);
            $model->setData('created_time', $this->date->gmtDate());
            try{
                $model->save();
                try{
                    // send email to admin
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    $adminEmail = $this->config->getValue('trans_email/ident_general/email', $storeScope);
                    $emailId = $this->config->getValue('simiconnector/sizeguide/email_template', $storeScope);
                    if ($adminEmail && $emailId) {
                        $this->inlineTranslation->suspend();
                        $postObject = new \Magento\Framework\DataObject();
                        $postObject->setData($model->getData());
                        $error = false;
                        // $contactSenderIdentity = $this->config->getValue('contact/email/sender_email_identity', $storeScope);
                        $sender = [
                            'name' => $this->config->getValue('trans_email/ident_sales/name', $storeScope),
                            'email' => $this->config->getValue('trans_email/ident_sales/email', $storeScope),
                        ];
                        $transport = $this->transportBuilder
                            ->setTemplateIdentifier($emailId)
                            ->setTemplateOptions([
                                'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                            ])
                            ->setTemplateVars(['data' => $postObject])
                            ->setFrom($sender)
                            ->addTo($adminEmail)
                            ->getTransport();
                        $transport->sendMessage();
                        $this->inlineTranslation->resume();
                    }
                }catch(\Exception $e){
                    $error = true;
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
     * Get Sizecharts by customer
     * @return array
     */
    public function getSizecharts(){
        $data = $this->request->getParams();
        if ($this->request->getContent()) {
            $data = json_decode($this->request->getContent(), true);
        }
        if (isset($data['customer_id']) && $data['customer_id']) 
        {
            // check to create new or update existed item
            $model = $this->simiObjectManager->get('\Simi\Simicustomize\Model\SizeChart');
            $collection = $model->getCollection();
            $collection->addFieldToFilter('customer_id', $data['customer_id'])->load();
            if ($collection->getSize()) {
                return ['data' => $collection->toArray()];
            }
            return ['data' => ['status' => false, 'message' => __('No data!')]];
        }
        return ['data' => ['status' => false, 'message' => __('Invalid request value!')]];
    }
}
