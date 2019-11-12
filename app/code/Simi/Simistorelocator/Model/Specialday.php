<?php

namespace Simi\Simistorelocator\Model;

class Specialday extends \Simi\Simistorelocator\Model\AbstractModelManageStores {

    /**
     * Model construct that should be used for object initialization.
     */
    public function _construct() {
        $this->_init('Simi\Simistorelocator\Model\ResourceModel\Specialday');
    }

    /**
     * Processing object before save data.
     */
    public function beforeSave() {
        $this->_prepareSaveWorkingTime();

        return parent::beforeSave();
    }

    /*
     * prepare save working time of specialday
     */

    protected function _prepareSaveWorkingTime() {
        if (is_array($this->getData('time_open'))) {
            $this->setData('time_open', implode(':', $this->getData('time_open')));
        }

        if (is_array($this->getData('time_close'))) {
            $this->setData('time_close', implode(':', $this->getData('time_close')));
        }
    }

}
