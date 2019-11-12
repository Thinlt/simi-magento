<?php

namespace Simi\Simistorelocator\Model\Config\Source;

class OrderTypeStore implements \Magento\Framework\Option\ArrayInterface {

    const SORT_BY_DEFAULT = 0;
    const SORT_BY_DISTANCE = 1;
    const SORT_BY_ALPHABETICAL = 2;

    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray() {
        return [
            ['value' => self::SORT_BY_DEFAULT, 'label' => __('Default')],
            ['value' => self::SORT_BY_DISTANCE, 'label' => __('Distance')],
            ['value' => self::SORT_BY_ALPHABETICAL, 'label' => __('Alphabetical order')],
        ];
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray() {
        return [
            self::SORT_BY_DEFAULT => __('Default'),
            self::SORT_BY_DISTANCE => __('Distance'),
            self::SORT_BY_ALPHABETICAL => __('Alphabetical order'),
        ];
    }

}
