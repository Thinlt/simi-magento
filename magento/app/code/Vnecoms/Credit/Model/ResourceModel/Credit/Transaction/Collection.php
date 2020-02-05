<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\ResourceModel\Credit\Transaction;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'transaction_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Credit\Model\Credit\Transaction', 'Vnecoms\Credit\Model\ResourceModel\Credit\Transaction');
        $this->addFilterToMap('created_at', 'main_table.created_at');
    }

}
