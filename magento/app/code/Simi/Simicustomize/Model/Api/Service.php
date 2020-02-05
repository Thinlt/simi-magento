<?php
namespace Simi\Simicustomize\Model\Api;

use Magento\Framework\App\Filesystem\DirectoryList;
use Simi\Simiconnector\Model\Api\Apiabstract;
use Simi\Simicustomize\Api\ServiceInterface;

class Service extends Apiabstract implements ServiceInterface
{
    const IMAGE_PATH = 'simiconnector/service';
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
    * @var \Magento\Framework\App\Filesystem\DirectoryList
    */
    protected $filesystem;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\RequestInterface $request
    ){
        $this->filesystem = $filesystem;
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
     * {@inheritdoc}
     */
    public function save() {
        $model = $this->simiObjectManager->get('\Simi\Simicustomize\Model\Service');
        $data = $this->request->getParams();
        if ($this->request->getContent()) {
            $data = json_decode($this->request->getContent(), true);
        }
        if (isset($data['email']) && isset($data['service'])) {
            // $collection = $model->getCollection();
            // if ($collection->getSize()) {
            //     $item = $collection->getFirstItem();
            //     if ($this->date->gmtDate('Y-m-d', $item->getData('date')) == $this->date->gmtDate('Y-m-d')) {
            //         $reserve = $item;
            //     }
            // }
            try{
                // save service text
                $optionText = $this->simiObjectManager->get('\Simi\Simicustomize\Model\Source\Service\ServiceType')->getOptionText($data['service']);
                $data['service_text'] = $optionText;
                $data['service_id'] = $data['service'];
                // move file uploaded via /rest/V1/simiconnector/uploadfile to new directory
                if (isset($data['files'])) {
                    $DS = DIRECTORY_SEPARATOR;
                    $files = is_string($data['files']) ? json_decode($data['files'], true) : $data['files'];
                    $media = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
                    $filePaths = [];
                    foreach($files as $item){
                        if (isset($item['title']) && isset($item['full_path'])) {
                            $file = self::IMAGE_PATH . $DS . md5($data['email']) . $DS;
                            if (!is_dir($media.$file)) mkdir($media.$file, 755, true);
                            $file = $file . str_replace(' ', '_', $item['title']);
                            if(rename($item['full_path'], $media . $file)){
                                $filePaths[] = $file;
                            }
                        }
                    }
                    $data['files'] = implode(',', $filePaths);
                }
                $model->setData($data);
                $incrementId = $model->getResource()->getNextIncrementId();
                $model->setIncrementId($incrementId);
                $model->setData('date', $this->date->gmtDate());
                $model->save();
                try{
                    $error = false;
                    // send email to admin
                    // $this->inlineTranslation->suspend();
                    $postObject = new \Magento\Framework\DataObject();
                    $postObject->setData($model->getData());
                    $postObject->setData('base_url', $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB));
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    $sender = [
                        'name' => $this->config->getValue('trans_email/ident_sales/name', $storeScope),
                        'email' => $this->config->getValue('trans_email/ident_sales/email', $storeScope),
                    ];
                    $adminEmail = $this->config->getValue('trans_email/ident_general/email', $storeScope);
                    $transport = $this->transportBuilder
                        ->setTemplateIdentifier($this->config->getValue('sales/service/email_template_admin', $storeScope))
                        ->setTemplateOptions([
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ])
                        ->setTemplateVars(['data' => $postObject])
                        ->setFrom($sender)
                        ->addTo($adminEmail)
                        ->getTransport();
                    $transport->sendMessage();
                    // $this->inlineTranslation->resume();
                    // send email to customer
                    // $this->inlineTranslation->suspend();
                    $customerEmail = $data['email'];
                    $data['name'] = isset($data['name']) ? $data['name'] : ucfirst(array_shift(explode('@', $data['email'])));
                    $postObject->setData('name', $data['name']);
                    $transport = $this->transportBuilder
                        ->setTemplateIdentifier($this->config->getValue('sales/service/email_template_customer', $storeScope))
                        ->setTemplateOptions([
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ])
                        ->setTemplateVars(['data' => $postObject])
                        ->setFrom($sender)
                        ->addTo($customerEmail)
                        ->getTransport();
                    $transport->sendMessage();
                    // $this->inlineTranslation->resume();
                }catch(\Exception $e){
                    return ['data' => ['status' => false, 'message' => $e->getMessage()]];
                }
                return true;
            }catch(\Exception $e){
                return ['data' => ['status' => false, 'error' => $e->getMessage()]];
            }
        }
        return ['data' => ['status' => false, 'error' => __('Invalid request value!')]];
    }

    protected function getFormatDate($date){
        return date('F d, Y', $this->date->gmtTimestamp($date));
    }
}
