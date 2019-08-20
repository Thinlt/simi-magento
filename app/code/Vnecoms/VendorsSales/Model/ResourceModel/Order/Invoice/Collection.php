<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Model\ResourceModel\Order\Invoice;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsSales\Model\Order\Invoice', 'Vnecoms\VendorsSales\Model\ResourceModel\Order\Invoice');
    }
    
    /**
     * Set order Filter.
     * @param array $conditions
     * @return $this
     */
    public function setOrderFilter($conditions)
    {
        $this->addFieldToFilter('vendor_order_id', $conditions);
        return $this;
    }
}
