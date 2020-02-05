<?php

namespace Simi\Simistorelocator\Model\ResourceModel\Schedule;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'schedule_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct() {
        $this->_init('Simi\Simistorelocator\Model\Schedule', 'Simi\Simistorelocator\Model\ResourceModel\Schedule');
    }

}
