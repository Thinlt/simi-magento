<?php

namespace Vnecoms\VendorsConfigApproval\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsConfigApproval\Model\ResourceModel\Config\CollectionFactory;
use Vnecoms\VendorsConfigApproval\Model\Config;

class FieldsetPrepareBefore implements ObserverInterface
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    
    /**
     * Configuration structure
     *
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $configStructure;
    
    /**
     * @var \Vnecoms\VendorsConfigApproval\Model\ResourceModel\Config\CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @param \Vnecoms\Vendors\Model\Session $session
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Vnecoms\Vendors\Model\Session $session,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Config\Model\Config\Structure $configStructure,
        CollectionFactory $collectionFactory
    ) {
        $this->session = $session;
        $this->request = $request;
        $this->configStructure = $configStructure;
        $this->collectionFactory = $collectionFactory;
    }

    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\App\ActionInterface
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getTransport();
        $fieldset = $transport->getFieldset();
        $section = $this->configStructure->getElement($this->request->getParam('section'));
        $sectionId = $section->getId();
        $groupId = str_replace($sectionId."_", '', $fieldset->getId());
        
        $fieldsByPath = [];
        foreach ($section->getChildren() as $group) {
            foreach ($group->getChildren() as $element) {
                $path = $element->getConfigPath() ?: $element->getPath();
                $fieldsByPath[$path] = $element;
            }
        }
  
        /* Get all data by the current section*/
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('vendor_id', $this->getVendor()->getId())
            ->addFieldToFilter('store_id', (int)$this->request->getParam('store'))
            ->addFieldToFilter('path', ['like' => $sectionId.'/%']);
        
        $configByPath = [];
        foreach($collection as $config){
            $field = $fieldsByPath[$config->getPath()];
            $value = $config->getValue();
            if ($field->hasBackendModel()) {
                $backendModel = $field->getBackendModel();
                $backendModel->setPath(
                    $path
                )->setValue(
                    $value
                )->afterLoad();
                $value = $backendModel->getValue();
            }
            $config->setValue($value);
            $configByPath[$config->getPath()] = $config;
        }
        
        $noticeMsg = __("The value of this field is changed but it has not been approved yet.");
        foreach ($fieldset->getElements() as $field) {
            $fieldId = str_replace($fieldset->getId()."_", '', $field->getId());
            $path = sprintf("%s/%s/%s", $sectionId, $groupId, $fieldId);
            if(isset($configByPath[$path])){
                $config = $configByPath[$path];
                $currentValue = $field->getValue()?$field->getValue():"NULL";
                $field->setValue($config->getValue());
                $field->setInherit(false);
                $fieldComment = __("Current working value is: %1", '<strong>'.$currentValue.'</strong>');
                $comment = sprintf('<span class="config-pending-approval">%s<br />%s</span>', $noticeMsg, $fieldComment);
                if($config->getStatus() == Config::STATUS_REJECTED){
                    $noticeMsg = $config->getNote()?$config->getNote():__("Your update for this field is rejected.");
                    $comment = sprintf(
                        '<span class="config-pending-approval">%s<br />%s<br /></span>',
                        '<span class="text-red">'.$noticeMsg.'</span>',
                        $fieldComment
                    );
                }
                $field->setComment($comment);
            }
        }
    }

    /**
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor()
    {
        return $this->session->getVendor();
    }
}
