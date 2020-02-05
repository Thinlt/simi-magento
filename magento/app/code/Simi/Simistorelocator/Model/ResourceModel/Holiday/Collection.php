<?php


namespace Simi\Simistorelocator\Model\ResourceModel\Holiday;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'holiday_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct() {
        $this->_init('Simi\Simistorelocator\Model\Holiday', 'Simi\Simistorelocator\Model\ResourceModel\Holiday');
    }

}
