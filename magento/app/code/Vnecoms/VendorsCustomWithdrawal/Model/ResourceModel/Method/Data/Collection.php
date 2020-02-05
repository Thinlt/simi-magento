<?php
namespace Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method\Data;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'data_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsCustomWithdrawal\Model\Method\Data', 'Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method\Data');
    }

}
