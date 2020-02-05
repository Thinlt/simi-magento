<?php

namespace Simi\Simistorelocator\Model\Config\Source;

class Unit implements \Magento\Framework\Option\ArrayInterface {

    const UNIT_MILES = 0;
    const UNIT_KILOMETER = 1;

    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray() {
        return [
            ['value' => self::UNIT_MILES, 'label' => __('Miles')],
            ['value' => self::UNIT_KILOMETER, 'label' => __('Kilometers')],
        ];
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray() {
        return [
            self::UNIT_MILES => __('Miles'),
            self::UNIT_KILOMETER => __('Kilometers'),
        ];
    }

}
