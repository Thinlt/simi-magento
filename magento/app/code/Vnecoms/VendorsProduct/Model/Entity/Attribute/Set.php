<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\Entity\Attribute;

class Set extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    
    protected $_eventPrefix = 'vendor_product_attribute_set';
    
    /**
     * @var \Vnecoms\VendorsProduct\Model\Entity\Attribute\GroupFactory
     */
    protected $_attrGroupFactory;
    
    /**
     * @var \Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\Group
     */
    protected $_attrGroupCollection;
    
    /**
     * @var \Vnecoms\VendorsProduct\Model\Entity\AttributeFactory
     */
    protected $_attributeFactory;
    
    
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $_resourceAttribute;
    
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\Set');
    }
    
    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $resourceAttribute,
        \Vnecoms\VendorsProduct\Model\Entity\Attribute\GroupFactory $attrGroupFactory,
        \Vnecoms\VendorsProduct\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_attrGroupFactory = $attrGroupFactory;
        $this->_attributeFactory = $attributeFactory;
        $this->_resourceAttribute = $resourceAttribute;
        
        return parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    /**
     * Collect data for save
     *
     * @param array $data
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function organizeData($data)
    {
        $modelGroupArray = [];
        $modelAttributeArray = [];
        $attributeIds = [];
        if ($data['attributes']) {
            $ids = [];
            foreach ($data['attributes'] as $attribute) {
                $ids[] = $attribute[0];
            }
            $attributeIds = $this->_resourceAttribute->getValidAttributeIds($ids);
        }
        if ($data['groups']) {
            foreach ($data['groups'] as $group) {
                $modelGroup = $this->initGroupModel($group);
    
                if ($data['attributes']) {
                    foreach ($data['attributes'] as $attribute) {
                        if ($attribute[1] == $group[0] && in_array($attribute[0], $attributeIds)) {
                            $modelAttribute = $this->_attributeFactory->create();
                            $modelAttribute->setAttributeId(
                                $attribute[0]
                            )->setAttributeGroupId(
                                $attribute[1]
                            )->setAttributeSetId(
                                $this->getId()
                            )->setSortOrder(
                                $attribute[2]
                            );
                            $modelAttributeArray[] = $modelAttribute;
                        }
                    }
                    $modelGroup->setAttributes($modelAttributeArray);
                    $modelAttributeArray = [];
                }
                $modelGroupArray[] = $modelGroup;
            }
            $this->setGroups($modelGroupArray);
        }
    
        if ($data['not_attributes']) {
            $modelAttributeArray = [];
            foreach ($data['not_attributes'] as $attributeId) {
                $modelAttribute = $this->_attributeFactory->create();
    
                $modelAttribute->setEntityAttributeId($attributeId);
                $modelAttributeArray[] = $modelAttribute;
            }
            $this->setRemoveAttributes($modelAttributeArray);
        }
    
        if ($data['removeGroups']) {
            $modelGroupArray = [];
            foreach ($data['removeGroups'] as $groupId) {
                $modelGroup = $this->_attrGroupFactory->create();
                $modelGroup->setId($groupId);
    
                $modelGroupArray[] = $modelGroup;
            }
            $this->setRemoveGroups($modelGroupArray);
        }
        $this->setAttributeSetName($data['attribute_set_name'])->setEntityTypeId($this->getEntityTypeId());
    
        return $this;
    }
    
    /**
     * @param array $group
     * @return Group
     */
    private function initGroupModel($group)
    {
        $modelGroup = $this->_attrGroupFactory->create();
        $modelGroup->load(
            is_numeric($group[0]) && $group[0] > 0 ? $group[0] : null
        )->setName(
            $group[1]
        )->setAttributeSetId(
            $this->getId()
        )->setSortOrder(
            $group[2]
        );
        return $modelGroup;
    }
    
    /**
     * Validate attribute set name
     *
     * @return bool
     * @throws LocalizedException
     */
    public function validate()
    {
        return true;
    }
    
    /**
     * Get Attribute Group Collection
     * @return \Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\Group
     */
    public function getGroupCollection()
    {
        if (!$this->_attrGroupCollection) {
            $this->_attrGroupCollection = $this->_attrGroupFactory->create()
                ->getResourceCollection()->setAttributeSetFilter(
                    $this->getId()
                )->setSortOrder()->load();
        }
        
        return $this->_attrGroupCollection;
    }
}
