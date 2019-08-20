<?php
namespace Vnecoms\VendorsProfileNotification\Model\ResourceModel\Process;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'process_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsProfileNotification\Model\Process', 'Vnecoms\VendorsProfileNotification\Model\ResourceModel\Process');
    }
}
