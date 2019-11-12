<?php

namespace Simi\Simistorelocator\Model\ResourceModel;

class Specialday extends \Simi\Simistorelocator\Model\ResourceModel\AbstractDbManageStores {

    /**
     * {@inheritdoc}
     */
    public function _construct() {
        $this->_init(\Simi\Simistorelocator\Setup\InstallSchema::SCHEMA_SPECIALDAY, 'specialday_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreRelationTable() {
        return $this->getTable(\Simi\Simistorelocator\Setup\InstallSchema::SCHEMA_STORE_SPECIALDAY);
    }

}
