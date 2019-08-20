<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\Group;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'group_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsProduct\Model\Entity\Attribute\Group', 'Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\Group');
    }

    /**
     * Set Attribute Set Filter
     * @param string|int|\Vnecoms\VendorsProduct\Model\Entity\Attribute\Set $set
     * @return \Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\Group\Collection
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
    
    /**
     * Set sort order
     * @return \Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\Group\Collection
     */
    public function setSortOrder()
    {
        $this->setOrder('sort_order', 'ASC');
        return $this;
    }
}
