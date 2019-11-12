<?php

namespace Simi\Simistorelocator\Model;

class Tag extends \Simi\Simistorelocator\Model\AbstractModelManageStores {

    const TAG_ICON_RELATIVE_PATH = 'simi/simistorelocator/images/tag';

    /**
     * Model construct that should be used for object initialization.
     */
    public function _construct() {
        $this->_init('Simi\Simistorelocator\Model\ResourceModel\Tag');
    }

}
