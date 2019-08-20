<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsConfig\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\Initial
     */
    protected $initialConfig;
    
    /**
     * @var \Vnecoms\VendorsConfig\Model\ConfigFactory
     */
    protected $configFactory;
    
    /**
     * Default config
     *
     * @var array
     */
    protected $defaultConfig;
    
    /**
     * TransactionFactory
     *
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $_transactionFactory;
    
    /**
     * System configuration structure
     *
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $configStructure;
    
    /**
     * Event dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @param \Vnecoms\VendorsConfig\App\Config\Initial $initialConfig
     * @param \Vnecoms\VendorsConfig\Model\ConfigFactory $configFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Context $context
     */
    public function __construct(
        \Vnecoms\VendorsConfig\App\Config\Initial $initialConfig,
        \Vnecoms\VendorsConfig\Model\ConfigFactory $configFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Context $context
    ) {
        $this->initialConfig = $initialConfig;
        $this->configFactory = $configFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->configStructure = $configStructure;
        $this->storeManager = $storeManager;
        $this->eventManager = $context->getEventManager();
        $config = $this->initialConfig->getData();
        $this->_processDefaultConfig($config);
    }
    
    /**
     * Process default config
     *
     * @param array $config
     * @return \Vnecoms\VendorsConfig\Helper\Data
     */
    protected function _processDefaultConfig($config = [])
    {
        $this->defaultConfig = isset($config['default'])?$config['default']:[];
        return $this;
    }
    
    /**
     * Get vendor configuration by path
     * @param string $path
     * @param int $vendorId
     */
    public function getVendorConfig($path, $vendorId, $storeId = null)
    {
        if($storeId === null){
            $storeId = $this->storeManager->getStore()->getId();
        }
        
        /* $config = $this->configFactory->create();
        $result = $config->getResource()->getConfigData($path, $vendorId, $storeId); */
        $collection = $this->configFactory->create()->getCollection()
            ->addFieldToFilter('path', $path)
            ->addFieldToFilter('vendor_id', $vendorId)
            ->addFieldToFilter('store_id', ['in' => [0, $storeId]])
            ->addOrder('store_id', 'DESC');
        
        if($collection->count()){
            return $collection->getFirstItem()->getValue();
        }
        
        $path = explode("/", $path);
        if (
            isset($path[0]) &&
            isset($path[1]) &&
            isset($path[2]) &&
            isset($this->defaultConfig[$path[0]][$path[1]][$path[2]])
        ) {
            return $this->defaultConfig[$path[0]][$path[1]][$path[2]];
        }
        return null;
    }
    
    /**
     * Get configuration value by path
     *
     * @param string $path
     * @param int $vendorId
     * @param int $storeId
     * @return array
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag
     */
    public function getConfigByPath($path, $vendorId, $storeId=null, $full = false)
    {
        $config = [];

        if($storeId === null){
            $storeId = $this->storeManager->getStore()->getId();
        }
        
        if (!$full) {
            /*Set default config*/
            $path = explode('/', trim($path));
            if (sizeof($path) == 1) {
                $defaultConfig = isset($this->defaultConfig[$path[0]])?$this->defaultConfig[$path[0]]:[];
                foreach ($defaultConfig as $key => $groupData) {
                    if (is_array($groupData)) {
                        foreach ($groupData as $fieldKey => $value) {
                            $pathConf = $path[0].'/'.$key.'/'.$fieldKey;
                            $config[$pathConf] = $value;
                        }
                    }
                }
            } elseif (sizeof($path) == 2) {
                $defaultConfig = isset($this->defaultConfig[$path[0]][$path[1]])?$this->defaultConfig[$path[0]][$path[1]]:[];
                foreach ($defaultConfig as $fieldKey => $value) {
                    $pathConf = $path[0].'/'.$path[1].'/'.$fieldKey;
                    $config[$pathConf] = $value;
                }
            }
        }
        $savedConfig = $this->getSavedConfigByPath($path, $vendorId, $storeId, $full);
        return array_merge($config, $savedConfig);
    }
    
    /**
     * Get saved config by path
     * 
     * @param string $path
     * @param int $vendorId
     * @param int $storeId
     * @param boolean $full
     * @return NULL[][]
     */
    public function getSavedConfigByPath($path, $vendorId, $storeId = null, $full = false){
        if($storeId === null){
            $storeId = $this->storeManager->getStore()->getId();
        }
        $config = [];
        $configDataCollection = $this->configFactory->create();
        $configDataCollection = $configDataCollection->getCollection()->addFieldToFilter('store_id', $storeId)->addPathFilter($path, $vendorId);
        $configDataCollection->load();
        foreach ($configDataCollection->getItems() as $data) {
            $config[$data->getPath()] = $full?['path' => $data->getPath(), 'value' => $data->getValue(), 'config_id' => $data->getConfigId(),]:$data->getValue();
        }
        return $config;
    }
    
    
    
    /**
     * Save config section
     * Require set: section, website, store and groups
     *
     * @param int $vendorId
     * @param string $sectionId
     * @param array $groups
     * @param int $storeId
     *
     * @throws \Exception
     * @return $this
     */
    public function saveConfig($vendorId, $sectionId, $groups, $storeId = 0)
    {
        if (!$sectionId || empty($groups)) {
            return $this;
        }
        $oldConfig = $this->getConfigByPath($sectionId, $vendorId, $storeId, true);
    
        /* @var $saveTransaction \Magento\Framework\DB\Transaction */
        $saveTransaction = $this->_transactionFactory->create();
        
        /* @var $deleteTransaction \Magento\Framework\DB\Transaction */
        $deleteTransaction= $this->_transactionFactory->create();
        
        // Extends for old config data
        $extraOldGroups = [];
    
        foreach ($groups as $groupId => $groupData) {
            $this->_processGroup(
                $vendorId,
                $groupId,
                $groupData,
                $groups,
                $sectionId,
                $storeId,
                $extraOldGroups,
                $oldConfig,
                $saveTransaction,
                $deleteTransaction
            );
        }
    
        try {
            $saveTransaction->save();    
            $deleteTransaction->delete();
            
            // website and store codes can be used in event implementation, so set them as well
            $this->eventManager->dispatch(
                "vendor_config_changed_section_{$sectionId}",
                ['vendor_id' => $vendorId]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    
        return $this;
    }

    /**
     *
     * @param int $vendorId
     * @param string $groupId
     * @param array $groupData
     * @param array $groups
     * @param string $sectionPath
     * @param int $storeId
     * @param array $extraOldGroups
     * @param array $oldConfig
     * @param \Magento\Framework\DB\Transaction $saveTransaction
     * @param \Magento\Framework\DB\Transaction $deleteTransaction
     */
    protected function _processGroup(
        $vendorId,
        $groupId,
        array $groupData,
        array $groups,
        $sectionPath,
        $storeId = 0,
        array &$extraOldGroups,
        array &$oldConfig,
        \Magento\Framework\DB\Transaction $saveTransaction,
        \Magento\Framework\DB\Transaction $deleteTransaction
    ) {
        $groupPath = $sectionPath . '/' . $groupId;

        /**
         *
         * Map field names if they were cloned
        */
        /** @var $group \Magento\Config\Model\Config\Structure\Element\Group */
        $group = $this->configStructure->getElement($groupPath);
    
        // set value for group field entry by fieldname
        // use extra memory
        $fieldsetData = [];
        if (isset($groupData['fields'])) {
            if ($group->shouldCloneFields()) {
                $cloneModel = $group->getCloneModel();
                $mappedFields = [];
    
                /** @var $field \Magento\Config\Model\Config\Structure\Element\Field */
                foreach ($group->getChildren() as $field) {
                    foreach ($cloneModel->getPrefixes() as $prefix) {
                        $mappedFields[$prefix['field'] . $field->getId()] = $field->getId();
                    }
                }
            }
            foreach ($groupData['fields'] as $fieldId => $fieldData) {                
                $fieldsetData[$fieldId] = is_array(
                    $fieldData
                ) && isset(
                    $fieldData['value']
                ) ? $fieldData['value'] : null;
            }


            foreach ($groupData['fields'] as $fieldId => $fieldData) {
                $originalFieldId = $fieldId;
                if ($group->shouldCloneFields() && isset($mappedFields[$fieldId])) {
                    $originalFieldId = $mappedFields[$fieldId];
                }
                /** @var $field \Magento\Config\Model\Config\Structure\Element\Field */
                $field = $this->configStructure->getElement($groupPath . '/' . $originalFieldId);
    
                /** @var \Vnecoms\VendorsConfig\Model\Config $backendModel */
                $backendModel = $field->hasBackendModel() ?
                    $field->getBackendModel() :
                    $this->configFactory->create();
    
                $data = [
                    'field' => $fieldId,
                    'groups' => $groups,
                    'group_id' => $group->getId(),
                    'field_config' => $field->getData(),
                    'fieldset_data' => $fieldsetData
                ];
                $backendModel->addData($data);
        
                if (false == isset($fieldData['value'])) {
                    $fieldData['value'] = null;
                }
    
                $path = $field->getGroupPath() . '/' . $fieldId;
                /**
                 * Look for custom defined field path
                 */
                if ($field && $field->getConfigPath()) {
                    $configPath = $field->getConfigPath();
                    if (!empty($configPath) && strrpos($configPath, '/') > 0) {
                        // Extend old data with specified section group
                        $configGroupPath = substr($configPath, 0, strrpos($configPath, '/'));
                        if (!isset($extraOldGroups[$configGroupPath])) {
                            $oldConfig = $this->extendConfig($configGroupPath, true, $oldConfig);
                            $extraOldGroups[$configGroupPath] = true;
                        }
                        $path = $configPath;
                    }
                }
        
                $backendModel->setVendorId($vendorId)
                    ->setPath($path)
                    ->setValue($fieldData['value'])
                    ->setStoreId($storeId);
    
                $inherit = isset($fieldData['inherit']);
                if(isset($oldConfig[$path])){
                    $backendModel->setConfigId($oldConfig[$path]['config_id']);
                    /**
                     * Delete config data if inherit
                     */
                    if (!$inherit) {
                        $saveTransaction->addObject($backendModel);
                    } else {
                        $deleteTransaction->addObject($backendModel);
                    }
                }elseif(!$inherit){
                    $backendModel->unsConfigId();
                    $saveTransaction->addObject($backendModel);
                }
            }
        }
    
        if (isset($groupData['groups'])) {
            foreach ($groupData['groups'] as $subGroupId => $subGroupData) {
                $this->_processGroup(
                    $vendorId,
                    $subGroupId,
                    $subGroupData,
                    $groups,
                    $groupPath,
                    $extraOldGroups,
                    $oldConfig,
                    $saveTransaction
                );
            }
        }
    }
}
