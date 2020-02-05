<?php

namespace Simi\Simistorelocator\Model\ResourceModel\Image;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * {@inheritdoc}
     */
    protected function _construct() {
        $this->_init('Simi\Simistorelocator\Model\Image', 'Simi\Simistorelocator\Model\ResourceModel\Image');
    }

}
