<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_attribute_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsProduct\Model\Entity\Attribute', 'Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute');
    }

    /**
     * Set Attribute Group Filter
     * @param \Vnecoms\VendorsProduct\Model\Entity\Attribute\Group|int|string $group
     * @return \Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\Collection
     */
    public function setAttributeGroupFilter($group)
    {
        $groupId = $group;
        if ($group instanceof \Vnecoms\VendorsProduct\Model\Entity\Attribute\Group) {
            $groupId = $group->getId();
        }
        $this->addFieldToFilter('attribute_group_id', $groupId);
        return $this;
    }
    
    /**
     * Set Attribute Group Filter
     * @param \Vnecoms\VendorsProduct\Model\Entity\Attribute\Set|int|string $set
     * @return \Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\Collection
     */
    public function setAttributeSetFilter($set)
    {
        $setId = $set;
        if ($set instanceof \Vnecoms\VendorsProduct\Model\Entity\Attribute\Set) {
            $setId = $set->getId();
        }
        $this->addFieldToFilter('attribute_set_id', $setId);
        return $this;
    }
}
