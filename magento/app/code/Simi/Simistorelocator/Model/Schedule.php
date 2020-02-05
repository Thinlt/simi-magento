<?php

namespace Simi\Simistorelocator\Model;

class Schedule extends \Simi\Simistorelocator\Model\AbstractModelManageStores {

    /**
     * Model construct that should be used for object initialization.
     */
    public function _construct() {
        $this->_init('Simi\Simistorelocator\Model\ResourceModel\Schedule');
    }

    /**
     * Processing object before save data.
     */
    public function beforeSave() {
        $this->_prepareSaveWeekdayTime();

        return parent::beforeSave();
    }

    /**
     * convert weekday time to string.
     *
     * @param $weekday
     * @param $suffix
     */
    protected function _convertWeekdayTime($weekday, $suffix) {
        if (is_array($this->getData($weekday . $suffix))) {
            $this->setData($weekday . $suffix, implode(':', $this->getData($weekday . $suffix)));
        }

        return $this->getData($weekday . $suffix);
    }

    /**
     * prepare save Weekday data.
     *
     * @param $weekday
     * @param $suffix
     */
    protected function _prepareSaveWeekdayTime() {
        $suffixes = ['_open', '_open_break', '_close_break', '_close'];
        foreach ($this->getWeekdays() as $weekday) {
            foreach ($suffixes as $suffix) {
                $this->_convertWeekdayTime($weekday, $suffix);
            }
        }
    }

    /**
     * get weekday code.
     *
     * @return array
     */
    public function getWeekdays() {
        return [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        ];
    }
}
