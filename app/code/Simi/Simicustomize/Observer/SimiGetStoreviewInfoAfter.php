<?php

namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;
use Simi\VendorMapping\Api\VendorListInterface;

class SimiGetStoreviewInfoAfter implements ObserverInterface {
    public $simiObjectManager;
    public $vendorList;
    protected $_attributeFactory;
    protected $eavConfig;
    protected $storeManager;
    protected $swatchMediaHelper;
    
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $config;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Swatches\Helper\Media $swatchMediaHelper,
        VendorListInterface $vendorList
    ) {
        $this->simiObjectManager = $simiObjectManager;
        $this->vendorList = $vendorList;
        $this->config = $config;
        $this->_attributeFactory = $attributeFactory;
        $this->eavConfig = $eavConfig;
        $this->storeManager = $storeManager;
        $this->swatchMediaHelper = $swatchMediaHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $object = $observer->getEvent()->getData('object');
        if ($object->storeviewInfo) {
            //TODO: will be sync with Ocean system in the future
            $object->storeviewInfo['vendor_list'] = $this->vendorList->getVendorList(); //get all vendors
            $object->storeviewInfo['delivery_returns'] = $this->config->getValue('sales/policy/delivery_returns'); //get all vendors
            $object->storeviewInfo['preorder_deposit'] = $this->config->getValue('sales/preorder/deposit_amount'); //get all vendors
            // add brands list to storeview api
            $attributeInfo = $this->_attributeFactory->getCollection();
            $attributeInfo->addFieldToFilter('attribute_code', 'brand');
            $storeId = $this->storeManager->getDefaultStoreView()->getStoreId();
            foreach($attributeInfo as $brand){
                $optionCollection = $this->simiObjectManager->get('\Magento\Eav\Model\Entity\Attribute\Option')->getCollection();
                $optionCollection
                    ->getSelect()
                    ->joinLeft(
                        ['value_table' => $optionCollection->getTable('eav_attribute_option_value')],
                        'value_table.option_id = main_table.option_id AND value_table.store_id = '.$storeId,
                        ['value_table.value AS name']
                    )
                    ->joinLeft(
                        ['swatch_table' => $optionCollection->getTable('eav_attribute_option_swatch')],
                        'swatch_table.option_id = main_table.option_id AND swatch_table.store_id = 0',
                        ['swatch_table.value AS value', 'swatch_table.option_id AS option_id']
                    )
                    ->where('attribute_id = ?', $brand->getAttributeId());
                foreach($optionCollection as $option){
                    $object->storeviewInfo['brands'][] = [
                        'option_id' => $option->getData('option_id'),
                        'name' => $option->getData('name'),
                        'image' => $this->swatchMediaHelper->getSwatchMediaUrl() . $option->getData('value'),
                        'attribute_name' => $brand->getData('frontend_label'),
                        'attribute_code' => $brand->getData('attribute_code'),
                        'attribute_id' => $brand->getData('attribute_id'),
                        'is_required' => $brand->getData('is_required'),
                    ];
                }
            }
        }
    }
}