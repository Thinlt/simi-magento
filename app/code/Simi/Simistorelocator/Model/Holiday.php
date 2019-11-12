<?php

namespace Simi\Simistorelocator\Model;

class Holiday extends \Simi\Simistorelocator\Model\AbstractModelManageStores {

    /**
     * Model construct that should be used for object initialization.
     */
    public function _construct() {
        $this->_init('Simi\Simistorelocator\Model\ResourceModel\Holiday');
    }

}
