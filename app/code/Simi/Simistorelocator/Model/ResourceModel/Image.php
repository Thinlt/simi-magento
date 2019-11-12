<?php

namespace Simi\Simistorelocator\Model\ResourceModel;

class Image extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    /**
     * {@inheritdoc}
     */
    public function _construct() {
        $this->_init(\Simi\Simistorelocator\Setup\InstallSchema::SCHEMA_IMAGE, 'image_id');
    }

}
