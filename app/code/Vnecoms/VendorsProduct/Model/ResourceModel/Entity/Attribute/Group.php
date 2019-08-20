<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute;

/**
 * Cms page mysql resource
 */
class Group extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_vendor_product_attribute_group', 'group_id');
    }
    
    /**
     * Checks if attribute group exists
     *
     * @param VES_VendorsProduct_Model_Entity_Attribute_Group $object
     * @return boolean
     */
    public function itemExists($object)
    {
        $adapter   = $this->getConnection();
        $bind      = array(
            'attribute_set_id'      => $object->getAttributeSetId(),
            'name'  => $object->getName()
        );
        $select = $adapter->select()
        ->from($this->getMainTable())
        ->where('attribute_set_id = :attribute_set_id')
        ->where('name = :name');
    
        return $adapter->fetchRow($select, $bind) > 0;
    }
    
    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $usedAttributeIds = [];
        if ($object->getAttributes()) {
            foreach ($object->getAttributes() as $attribute) {
                $attribute->setAttributeGroupId($object->getId());
                $attribute->save();
                $usedAttributeIds[] = $attribute->getId();
            }
        }
        
        /* Delete all attributes which are not used */
        if(sizeof($usedAttributeIds)){
            $this->getConnection()->delete(
                $this->getTable('ves_vendor_product_entity_attribute'), 'attribute_group_id ="'.$object->getId().'" AND entity_attribute_id NOT IN('.implode(',', $usedAttributeIds).')'
            );
        }
        
        return parent::_afterSave($object);
    }

}
