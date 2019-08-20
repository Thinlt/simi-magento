<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Block\Adminhtml\Product\Attribute\Set;

use Magento\Catalog\Model\Entity\Product\Attribute\Group\AttributeMapperInterface;
use Vnecoms\VendorsProduct\Model\Entity\Product\Attribute\Group\AttributeMapper;

/**
 * Adminhtml Catalog Attribute Set Main Block
 *
 */

class Main extends \Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Main
{
    /**
     * @var string
     */
    protected $_template = 'Vnecoms_VendorsProduct::catalog/product/attribute/set/main.phtml';

    /**
     * @var Vnecoms\VendorsProduct\Model\Entity\Product\Attribute\Group\AttributeMapper
     */
    protected $_newAttributeMapper;
    
    /**
     * @var \Vnecoms\VendorsProduct\Model\Entity\Attribute\SetFactory
     */
    protected $_customAttributeSetFactory;
    
    /**
     * @var \Vnecoms\VendorsProduct\Model\Entity\Attribute\Group
     */
    protected $_customGroupFactory;
    
    /**
     * Attribute collection
     * @var \Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\CollectionFactory
     */
    protected $_customCollectionFactory;
    
    /**
     * Prepare Global Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $data = [];
        $block = 'Vnecoms\VendorsProduct\Block\Adminhtml\Product\Attribute\Set\Main\Formset';
        $block = $this->getLayout()->createBlock(
            $block,
            $this->getNameInLayout() . '.vendor_edit_set_form',
            ['data' => $data]
        );
        $this->setChild('edit_set_form', $block);
        
        $backButton = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button',
            $this->getNameInLayout() . '.back_button',
            ['data' => [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('vendors/*/') . '\')',
                'class' => 'back'
            ]
            ]
        );
        $this->getToolbar()->setChild('back_button', $backButton);
        
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button',
            $this->getNameInLayout() . '.delete_button',
            ['data' => [
                    'label' => __('Restore to Default'),
                    'onclick' => 'deleteConfirm(\'' . $this->escapeJsQuote(
                        __(
                            'You are about to restore this product form to default form. '
                            . 'Are you sure you want to do that?'
                        )
                    ) . '\', \'' . $this->getUrl(
                        'vendors/*/delete',
                        ['id' => $this->_getSetId()]
                    ) . '\')',
                    'class' => 'delete'
                ]
            ]
        );
        $this->getToolbar()->setChild('delete_button', $button);
    }
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Eav\Model\Entity\TypeFactory $typeFactory
     * @param \Magento\Eav\Model\Entity\Attribute\GroupFactory $groupFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param AttributeMapperInterface $attributeMapper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Eav\Model\Entity\TypeFactory $typeFactory,
        \Magento\Eav\Model\Entity\Attribute\GroupFactory $groupFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        AttributeMapperInterface $attributeMapper,
        AttributeMapper $newAttributeMapper,
        \Vnecoms\VendorsProduct\Model\Entity\Attribute\SetFactory $customAttributeSetFactory,
        \Vnecoms\VendorsProduct\Model\Entity\Attribute\GroupFactory $customGroupFactory,
        \Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\CollectionFactory $customCollectionFactory,
        array $data = []
    ) {
        $this->_newAttributeMapper = $newAttributeMapper;
        $this->_customAttributeSetFactory = $customAttributeSetFactory;
        $this->_customGroupFactory = $customGroupFactory;
        $this->_customCollectionFactory = $customCollectionFactory;
        
        parent::__construct(
            $context,
            $jsonEncoder,
            $typeFactory,
            $groupFactory,
            $collectionFactory,
            $registry,
            $attributeMapper
        );
    }
    
    /**
     * Get attribute set.
     * @return Ambigous <\Magento\Eav\Model\Entity\Attribute\Set, \Magento\Framework\mixed, NULL, multitype:>
     */
    public function getAttributeSet()
    {
        return $this->_getAttributeSet();
    }
    
    /**
     * Retrieve Attribute Set Save URL
     *
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('vendors/catalog_product_set/save', ['id' => $this->_getSetId()]);
    }
    
    /**
     * Retrieve Attribute Set Group Tree as JSON format
     *
     * @return string
     */
    public function getGroupTreeJson()
    {
        $setId = $this->_getSetId();
        $customSet = $this->_customAttributeSetFactory->create();
        $customSet->load($setId, 'parent_set_id');
        
        if (!$customSet->getId()) {
            return $this->getDefaultGroupTreeJson($setId);
        }
        
        $setId = $customSet->getId();
        
        $items = [];
        /* @var $groups \Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\Group\Collection */
        $groups = $this->_customGroupFactory->create()->getResourceCollection()->setAttributeSetFilter(
            $setId
        )->setSortOrder()->load();
        
        /* @var $node \Magento\Eav\Model\Entity\Attribute\Group */
        foreach ($groups as $node) {
            $item = [];
            $item['text'] = $node->getName();
            $item['id'] = $node->getGroupId();
            $item['cls'] = 'folder';
            $item['allowDrop'] = true;
            $item['allowDrag'] = true;
        
            $nodeChildren = $this->_customCollectionFactory->create()->setAttributeGroupFilter(
                $node->getId()
            )->load();
            $attributeIds = $nodeChildren->getColumnValues('attribute_id');
            
            $nodeChildren = $this->_collectionFactory->create();
            $nodeChildren->join(
                ['entity_attribute' => $nodeChildren->getTable('ves_vendor_product_entity_attribute')],
                'entity_attribute.attribute_id = main_table.attribute_id AND attribute_group_id="'.$node->getId().'"'
            )->addFieldToFilter(
                'main_table.attribute_id',
                ['in'=>$attributeIds]
            )->setOrder('sort_order', 'ASC')->addVisibleFilter()->load();

            if ($nodeChildren->getSize() > 0) {
                $item['children'] = [];
                foreach ($nodeChildren->getItems() as $child) {
                    $item['children'][] = $this->_newAttributeMapper->map($child);
                }
            }
        
            $items[] = $item;
        }
        return $this->_jsonEncoder->encode($items);
    }
    
    public function getDefaultGroupTreeJson($setId)
    {
        $items = [];
        /* @var $groups \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection */
        $groups = $this->_groupFactory->create()->getResourceCollection()->setAttributeSetFilter(
            $setId
        )->setSortOrder()->load();
        
        /* @var $node \Magento\Eav\Model\Entity\Attribute\Group */
        foreach ($groups as $node) {
            $item = [];
            $item['text'] = $node->getAttributeGroupName();
            $item['id'] = $node->getAttributeGroupId();
            $item['cls'] = 'folder';
            $item['allowDrop'] = true;
            $item['allowDrag'] = true;
        
            $nodeChildren = $this->_collectionFactory->create()->setAttributeGroupFilter(
                $node->getId()
            )->addVisibleFilter()->load();
        
            if ($nodeChildren->getSize() > 0) {
                $item['children'] = [];
                foreach ($nodeChildren->getItems() as $child) {
                    $item['children'][] = $this->_newAttributeMapper->map($child);
                }
            }
        
            $items[] = $item;
        }
        
        return $this->_jsonEncoder->encode($items);
    }
    
    /**
     * Retrieve Unused in Attribute Set Attribute Tree as JSON
     *
     * @return string
     */
    public function getAttributeTreeJson()
    {
        $items = [];
        $setId = $this->_getSetId();
        $customAttributeSet = $this->_customAttributeSetFactory->create();
        $customAttributeSet->load($setId, 'parent_set_id');
        if ($customAttributeSet->getId()) {
            $customCollection = $this->_customCollectionFactory->create()->setAttributeSetFilter($customAttributeSet->getId());
            $excludeAttributeIds = $customCollection->getColumnValues('attribute_id');
    
            $attributes = $this->_collectionFactory->create()->setAttributeSetFilter($setId)
                ->setAttributesExcludeFilter($excludeAttributeIds)
                ->addVisibleFilter()->load();
    
        
            foreach ($attributes as $child) {
                $attr = [
                    'text' => $child->getAttributeCode(),
                    'id' => $child->getAttributeId(),
                    'cls' => 'leaf',
                    'allowDrop' => false,
                    'allowDrag' => true,
                    'leaf' => true,
                    'is_user_defined' => $child->getIsUserDefined(),
                    'entity_id' => $child->getEntityId(),
                ];
        
                $items[] = $attr;
            }
        }
        
        if (count($items) == 0) {
            $items[] = [
                'text' => __('Empty'),
                'id' => 'empty',
                'cls' => 'folder',
                'allowDrop' => false,
                'allowDrag' => false,
            ];
        }
    
        return $this->_jsonEncoder->encode($items);
    }
}
