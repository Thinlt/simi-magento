<?php

namespace Simi\Simistorelocator\Model\Schedule\Option;

class WeekdayStatus implements \Magento\Framework\Data\OptionSourceInterface, \Simi\Simistorelocator\Model\Data\Option\OptionHashInterface {

    const WEEKDAY_STATUS_OPEN = 1;
    const WEEKDAY_STATUS_CLOSE = 2;

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray() {
        return [
            ['label' => __('Open'), 'value' => self::WEEKDAY_STATUS_OPEN],
            ['label' => __('Close'), 'value' => self::WEEKDAY_STATUS_CLOSE],
        ];
    }

    /**
     * Return array of options as key-value pairs.
     *
     * @return array Format: array('<key>' => '<value>', '<key>' => '<value>', ...)
     */
    public function toOptionHash() {
        return [
            self::WEEKDAY_STATUS_OPEN => __('Open'),
            self::WEEKDAY_STATUS_CLOSE => __('Close'),
        ];
    }

}
