<?php

namespace Vnecoms\VendorsConfigApproval\Block\Adminhtml\Config;

use Vnecoms\VendorsConfigApproval\Model\ResourceModel\Config\CollectionFactory;
use Magento\Config\Model\Config\Structure\Reader as ConfigReader;
use Vnecoms\VendorsConfig\Helper\Data as ConfigHelper;
use Vnecoms\VendorsConfigApproval\Model\Config as ConfigApproval;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Configuration structure
     *
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $configStructure;
    
    /**
     * @var ConfigReader
     */
    protected $configReader;
    
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var array
     */
    protected $usedSections;
    
    /**
     * @var array
     */
    protected $usedFieldsets;
    
    /**
     * @var array
     */
    protected $fieldsByPath;
    
    /**
     * @var ConfigHelper
     */
    protected $configHelper;
    
    /**
     * @var array
     */
    protected $sourceModelOptions;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ConfigReader $configReader
     * @param CollectionFactory $collectionFactory
     * @param ConfigHelper $configHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        ConfigReader $configReader,
        CollectionFactory $collectionFactory,
        ConfigHelper $configHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->configReader = $configReader;
        $this->collectionFactory = $collectionFactory;
        $this->configHelper = $configHelper;
        $this->objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Vnecoms_VendorsConfigApproval';
        $this->_controller = 'adminhtml_config';

        parent::_construct();
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('save');
        $this->initUpdates();
    }

    /**
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __("Review config changes from vendor '%1'", $this->escapeHtml($this->getVendor()->getVendorId()));
    }
    
    /**
     * Init updates
     */
    public function initUpdates()
    {
        $updateCollection = $this->collectionFactory->create();
        $updateCollection->addFieldToFilter('vendor_id', $this->getVendor()->getId());
        foreach($updateCollection as $update){
            $path = explode("/", $update->getPath());
            if(!isset($this->usedSections[$path[0]])){
                $this->usedSections[] = $path[0];
            }
            
            if(!isset($this->usedFieldsets[$path[0].'/'.$path[1]])){
                $this->usedFieldsets[] = $path[0].'/'.$path[1];
            }
            $this->fieldsByPath[$update->getPath()] = $update;
        }
    }
    
    /**
     * Get used sections
     * 
     * @return multitype:
     */
    public function getUsedSections(){
        return $this->usedSections;
    }
    
    /**
     * Get used Fieldsets
     * 
     * @return multitype:
     */
    public function getUsedFieldsets(){
        return $this->usedFieldsets;
    }
    
    /**
     * Get Fields by Path
     * 
     * @return multitype:
     */
    public function getUpdatesByPath(){
        return $this->fieldsByPath;
    }
    
    /**
     * Get vendor config
     * 
     * @param string $path
     */
    public function getVendorConfig($path){
        return $this->configHelper->getVendorConfig($path, $this->getVendor()->getId());
    }
    /**
     * Get vendor config sections
     * 
     * @return array
     */
    public function getVendorConfigSections(){
        $config = $this->configReader->read('vendors');
        return $config['config']['system']['sections'];
    }
    
    /**
     * Get Source model options
     * 
     * @param string $sourceModelClassName
     * @return array
     */
    public function getSourceModelOptions($sourceModelClassName){
        if(!isset($this->sourceModelOptions[$sourceModelClassName])){
            $sourceModel = $this->objectManager->get($sourceModelClassName);
            $_options = [];
            foreach ($sourceModel->toOptionArray() as $option) {
                $_options[$option['value']] = $option['label'];
            }
            $this->sourceModelOptions[$sourceModelClassName] = $_options;
        }
        
        return $this->sourceModelOptions[$sourceModelClassName];
    }
    
    /**
     * Get base url
     * 
     * @param string $type
     */
    public function getBaseUrlByType($type = 'media'){
        return $this->_storeManager->getStore()->getBaseUrl($type);
    }
    
    /**
     * Get Product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getVendor()
    {
        return $this->_coreRegistry->registry('current_vendor');
    }

    /**
     * @return string
     */
    public function getRejectUrl()
    {
        return $this->getUrl('*/*/reject');
    }
    
    /**
     * Get save change URL
     * 
     * @return string
     */
    public function getSaveChangeUrl(){
        return $this->getUrl('*/*/saveChange');
    }
    
    /**
     * @return string
     */
    public function getApproveUrl(ConfigApproval $config)
    {
        return $this->getUrl('*/*/approve', ['id' => $config->getId()]);
    }
}
