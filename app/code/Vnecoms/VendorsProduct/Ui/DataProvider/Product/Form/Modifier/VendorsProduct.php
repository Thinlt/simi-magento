<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Vnecoms\VendorsProduct\Helper\Data as VendorProductHelper;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Data provider for "Customizable Options" panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VendorsProduct extends AbstractModifier
{
    /**
     * @var \Vnecoms\VendorsProduct\Helper\Data
     */
    protected $_vendorProductHelper;
    
    /**
     * Set collection factory
     *
     * @var AttributeSetCollectionFactory
     */
    protected $_attributeSetCollectionFactory;
    
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     * @since 101.0.0
     */
    protected $arrayManager;

    /**
     * VendorsProduct constructor.
     * @param LocatorInterface $locator
     * @param VendorProductHelper $vendorProductHelper
     * @param ArrayManager $arrayManager
     * @param AttributeSetCollectionFactory $attributeSetCollectionFactory
     */
    public function __construct(
        LocatorInterface $locator,
        VendorProductHelper $vendorProductHelper,
        ArrayManager $arrayManager,
        AttributeSetCollectionFactory $attributeSetCollectionFactory
    ) {
        $this->locator = $locator;
        $this->_vendorProductHelper = $vendorProductHelper;
        $this->arrayManager = $arrayManager;
        $this->_attributeSetCollectionFactory = $attributeSetCollectionFactory;
        
        return $this;
    }
    /**
     * @var array
     */
    protected $_meta = [];
    

    public function modifyData(array $data)
    {
        return $data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->_meta = $meta;
        
        $this->removeNotUsedSections();
        $this->removeNotusedAttributes();
        $this->updateCustomOptionsJs();
        $this->removeDefaultNewCategoryButton();
        $this->updateAttributeSetField();
        
        return $this->_meta;
    }
    
    /**
     * Remove not used sections
     */
    public function removeNotUsedSections()
    {
        if (isset($this->_meta['schedule-design-update'])) {
            unset($this->_meta['schedule-design-update']);
        }
        if (isset($this->_meta['design'])) {
            unset($this->_meta['design']);
        }
        
        if (!$this->_vendorProductHelper->canVendorSetWebsite() && isset($this->_meta['websites'])) {
            unset($this->_meta['websites']);
        }

    }
    
    /**
     * Remove not used attributes
     *
     * @param void
     * @return void
     */
    public function removeNotusedAttributes()
    {
        $notAllowedAttributes = $this->_vendorProductHelper->getNotUsedVendorAttributes();
        
        foreach ($notAllowedAttributes as $attributeCode) {
            $path = $this->arrayManager->findPath('container_' . $attributeCode, $this->_meta, null, 'children');
            if ($path !== null) {
                $pathOfNotUsedAttribute = $path;
                $this->_meta = $this->arrayManager->remove($pathOfNotUsedAttribute, $this->_meta, ArrayManager::DEFAULT_PATH_DELIMITER);
                $parentPath = $this->getParentPath($path);
                $data = $this->arrayManager->get($parentPath, $this->_meta);
                if(!is_array($data) || !sizeof($data)){
                    $this->_meta = $this->arrayManager->remove($this->getParentPath($parentPath), $this->_meta, ArrayManager::DEFAULT_PATH_DELIMITER);
                }
            }
        }
    }
    
    /**
     * Get parent path
     * 
     * @param string $path
     * @return string
     */
    protected function getParentPath($path){
        $parentPath = explode(ArrayManager::DEFAULT_PATH_DELIMITER, $path);
        array_pop($parentPath);
        $parentPath = implode(ArrayManager::DEFAULT_PATH_DELIMITER, $parentPath);
        
        return $parentPath;
    }
    
    /**
     * Update custom options js
     */
    public function updateCustomOptionsJs()
    {
       if(isset($this->_meta['custom_options'])){
           $this->_meta['custom_options']['children']['options']['children']['record']
           ['children']['container_option']['children']['container_common']
           ['children']['type']['arguments']['data']['config']['component']
               = 'Vnecoms_VendorsProduct/js/custom-options-type';

           $this->_meta['custom_options']['children']['options']['children']['record']
           ['children']['container_option']['children']['container_common']
           ['children']['title']['arguments']['data']['config']['component']
               = 'Vnecoms_VendorsProduct/component/static-type-input';
       }

    }
    
    /**
     * Remove the default New Category button
     */
    public function removeDefaultNewCategoryButton()
    {
        if (isset($this->_meta['product-details']['children']['container_category_ids']
            ['children']['create_category_button'])
        ) {
            unset($this->_meta['product-details']['children']['container_category_ids']['children']['create_category_button']);
        }
    }
    
    public function updateAttributeSetField()
    {
        if ($name = $this->getGeneralPanelName($this->_meta)) {
            $this->_meta[$name]['children']['attribute_set_id']['arguments']['data']
                ['config']['options'] = $this->getAttributeSetOptions();
        }
    }
    /**
     * Return options for select
     *
     * @return array
     */
    public function getAttributeSetOptions()
    {
        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $collection */
        $collection = $this->_attributeSetCollectionFactory->create();
        $collection->setEntityTypeFilter($this->locator->getProduct()->getResource()->getTypeId())
        ->addFieldToSelect('attribute_set_id', 'value')
        ->addFieldToSelect('attribute_set_name', 'label')
        ->addFieldToFilter('attribute_set_id', ['nin'=>$this->_vendorProductHelper->getAttributeSetRestriction()])
        ->setOrder(
            'attribute_set_name',
            \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection::SORT_ORDER_ASC
        );
    
        return $collection->getData();
    }
}
