<?php

namespace Simi\Simistorelocator\Model\ResourceModel\Specialday;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'specialday_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct() {
        $this->_init('Simi\Simistorelocator\Model\Specialday', 'Simi\Simistorelocator\Model\ResourceModel\Specialday');
    }

}
