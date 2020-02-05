<?php
namespace Simi\Simicustomize\Model\Api;

class Contact extends \Simi\Simiconnector\Model\Api\Apiabstract implements \Simi\Simicustomize\Api\ContactInterface
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
     * Save Reserve request
     * @return boolean
     */
    public function index() {
        $model = $this->simiObjectManager->get('\Simi\Simicustomize\Model\Contact');
        $data = $this->request->getParams();
        if ($this->request->getContent()) {
            $data = json_decode($this->request->getContent(), true);
        }
        if (isset($data['name']) && $data['name'] && isset($data['phone']) && $data['phone']) {
            $model->setData('name', $data['name']);
            $model->setData('phone', $data['phone']);
            $model->setData('time', $data['time']);
            $model->setData('created_time', $this->date->gmtDate());
            // check to create new or update existed item
            $collection = $model->getCollection();
            $collection->addFieldToFilter('name', $data['name'])
                ->addFieldToFilter('phone', $data['phone'])
                ->load();
            if ($collection->getSize()) {
                $item = $collection->getFirstItem();
                // compare with date format 'Y-m-d H:i:s'
                if ($this->date->gmtDate('Y-m-d', $item->getData('created_time')) == $this->date->gmtDate('Y-m-d')) {
                    $model = $item;
                }
            }
            try{
                $model->save();
                try{
                    // send email to admin
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    $adminEmail = $this->config->getValue('contact/email/recipient_email', $storeScope);
                    if ($adminEmail) {
                        $this->inlineTranslation->suspend();
                        $postObject = new \Magento\Framework\DataObject();
                        $postObject->setData($model->getData());
                        // $postObject->setData('customer_email', $customer->getEmail());
                        // $postObject->setData('email', $customer->getEmail());
                        $error = false;
                        $contactSenderIdentity = $this->config->getValue('contact/email/sender_email_identity', $storeScope);
                        $sender = [
                            'name' => $this->config->getValue('trans_email/ident_'.$contactSenderIdentity.'/name', $storeScope),
                            'email' => $this->config->getValue('trans_email/ident_'.$contactSenderIdentity.'/email', $storeScope),
                        ];
                        $transport = $this->transportBuilder
                            ->setTemplateIdentifier($this->config->getValue('contact/email/email_template', $storeScope))
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
                    // return ['data' => ['status' => false, 'message' => $e->getMessage()]];
                }
                return true;
            }catch(\Exception $e){
                return ['data' => ['status' => false, 'message' => $e->getMessage()]];
            }
        }
        return ['data' => ['status' => false, 'message' => __('Invalid request value!')]];
    }
}
