<?php

namespace Simi\Simistorelocator\Model\ResourceModel\Tag;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'tag_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct() {
        $this->_init('Simi\Simistorelocator\Model\Tag', 'Simi\Simistorelocator\Model\ResourceModel\Tag');
    }

}
