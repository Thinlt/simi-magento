<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsProduct\Block\Vendors\Product\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Accordion;
use Vnecoms\Vendors\Block\Vendors\Widget\Tabs as WigetTabs;
use Magento\Backend\Model\Auth\Session;
use Magento\Catalog\Helper\Catalog;
use Magento\Catalog\Helper\Data;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Vnecoms\VendorsProduct\Model\Entity\Attribute\Set as VendorProductAttributeSet;

/**
 * Admin product edit tabs
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Tabs extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs
{
    const BASIC_TAB_GROUP_CODE = 'basic';

    const ADVANCED_TAB_GROUP_CODE = 'advanced';

    /**
     * @var Vnecoms\VendorsProduct\Model\Entity\Attribute\Set
     */
    protected $_attributeSet;
    
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory;
    
    /**
     * @var \Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\CollectionFactory
     */
    protected $_customAttributeCollectionFactory;
    
    
    /**
     * @var string
     */
    protected $_attributeTabBlock = 'Vnecoms\VendorsProduct\Block\Vendors\Product\Edit\Tab\Attributes';

    /**
     * @var string
     */
    protected $_template = 'Vnecoms_VendorsProduct::product/edit/tabs.phtml';

    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        Manager $moduleManager,
        CollectionFactory $collectionFactory,
        Catalog $helperCatalog,
        Data $catalogData,
        Registry $registry,
        InlineInterface $translateInline,
        VendorProductAttributeSet $vendorProductAttributeSet,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\CollectionFactory $customAttributeCollectionFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $jsonEncoder,
            $authSession,
            $moduleManager,
            $collectionFactory,
            $helperCatalog,
            $catalogData,
            $registry,
            $translateInline
        );
        
        $product = $this->getProduct();
        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        
        $vendorProductAttributeSet->load($setId, 'parent_set_id');
        $this->_attributeSet = $vendorProductAttributeSet;
        
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
        $this->_customAttributeCollectionFactory = $customAttributeCollectionFactory;
    }
    
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {
        
        /*Just show the default form*/
        if (!$this->_attributeSet->getId()) {
            parent::_prepareLayout();
            $this->removeTab('websites');
            $this->removeTab('related');
            $this->removeTab('upsell');
            $this->removeTab('crosssell');
            $this->removeTab('design');
            return $this;
        }
        
        $this->_prepareTabs();
        
        /* Don't display website tab for single mode */
        if (!$this->_storeManager->isSingleStoreMode()) {
//             $this->setTabData(
//                 'websites',
//                 'content',
//                 $this->_translateHtml(
//                     $this->getLayout()->createBlock(
//                         'Vnecoms\VendorsProduct\Block\Vendors\Product\Edit\Tab\Websites'
//                     )->toHtml()
//                 )
//             );
        }
        
        return $this;
    }
    
    
    protected function _prepareTabs()
    {
        $product = $this->getProduct();
        

        $tabAttributesBlock = $this->getLayout()->createBlock(
            $this->getAttributeTabBlock(),
            $this->getNameInLayout() . '_attributes_tab'
        );
        $advancedGroups = [];
    
        foreach ($this->_attributeSet->getGroupCollection() as $group) {
            /** @var $group \Vnecoms\VendorsProduct\Model\Entity\Attribute\Group*/
            $attributes = $this->getAttributeCollection($group->getId());
            $availableAttributes = [];
            foreach ($attributes as $key => $attribute) {
                $applyTo = $attribute->getApplyTo();
                if ($attribute->getIsVisible() && (empty($applyTo) || in_array($product->getTypeId(), $applyTo))
                ) {
                    $availableAttributes[] = $attribute;
                }
            }
    
            if ($availableAttributes) {
                $tabData = [
                    'label' => __($group->getAttributeGroupName()),
                    'content' => $this->_translateHtml(
                        $tabAttributesBlock->setGroup($group)->setGroupAttributes($attributes)->toHtml()
                    ),
                    'class' => 'user-defined',
                    'group_code' => $group->getTabGroupCode() ?: self::BASIC_TAB_GROUP_CODE,
                ];
                if ($tabData['group_code'] === self::BASIC_TAB_GROUP_CODE) {
                    $this->addTab($group->getAttributeGroupCode(), $tabData);
                } else {
                    $advancedGroups[$group->getAttributeGroupCode()] = $tabData;
                }
            }
        }
//         /* Don't display website tab for single mode */
//         if (!$this->_storeManager->isSingleStoreMode()) {
//             $this->addTab(
//                 'websites',
//                 [
//                     'label' => __('Websites'),
//                     'content' => $this->_translateHtml(
//                         $this->getLayout()->createBlock(
//                             'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Websites'
//                         )->toHtml()
//                     ),
//                     'group_code' => self::BASIC_TAB_GROUP_CODE
//                 ]
//             );
//         }
    
//         if (isset($advancedGroups['advanced-pricing'])) {
//             $this->addTab('advanced-pricing', $advancedGroups['advanced-pricing']);
//             unset($advancedGroups['advanced-pricing']);
//         }
    
        if ($this->_moduleManager->isEnabled('Magento_CatalogInventory')
            && $this->getChildBlock('advanced-inventory')
        ) {
            $this->addTab(
                'advanced-inventory',
                [
                    'label' => __('Advanced Inventory'),
                    'content' => $this->_translateHtml(
                        $this->getChildHtml('advanced-inventory')
                    ),
                    'group_code' => self::ADVANCED_TAB_GROUP_CODE
                ]
            );
        }
    
//         /**
//          * Do not change this tab id
//          */
//         if ($this->getChildBlock('customer_options')) {
//             $this->addTab('customer_options', 'customer_options');
//             $this->getChildBlock('customer_options')->setGroupCode(self::ADVANCED_TAB_GROUP_CODE);
//         }
    
    
//         if (isset($advancedGroups['design'])) {
//             $this->addTab('design', $advancedGroups['design']);
//             unset($advancedGroups['design']);
//         }
    
//         if ($this->getChildBlock('product-alerts')) {
//             $this->addTab('product-alerts', 'product-alerts');
//             $this->getChildBlock('product-alerts')->setGroupCode(self::ADVANCED_TAB_GROUP_CODE);
//         }
    
//         if (isset($advancedGroups['autosettings'])) {
//             $this->addTab('autosettings', $advancedGroups['autosettings']);
//             unset($advancedGroups['autosettings']);
//         }
    
//         foreach ($advancedGroups as $groupCode => $group) {
//             $this->addTab($groupCode, $group);
//         }
    }
    
    /**
     * Get attribute collection
     * @param int $groupId
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    public function getAttributeCollection($groupId)
    {
        $customAttrCollection = $this->_customAttributeCollectionFactory->create()->setAttributeGroupFilter(
            $groupId
        )->load();
        $attributeIds = $customAttrCollection->getColumnValues('attribute_id');
        $attrCollection = $this->_attributeCollectionFactory->create();
        $attrCollection->join(
            ['entity_attribute' => $attrCollection->getTable('ves_vendor_product_entity_attribute')],
            'entity_attribute.attribute_id = main_table.attribute_id AND attribute_group_id="'.$groupId.'"'
        )->addFieldToFilter(
            'main_table.attribute_id',
            ['in'=>$attributeIds]
        )->setOrder('sort_order', 'ASC')->addVisibleFilter()->load();
        
        return $attrCollection;
    }
}
