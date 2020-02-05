<?php

namespace Simi\Simistorelocator\Model\ResourceModel\Store\Grid;

class StatusesArray implements \Magento\Framework\Option\ArrayInterface {

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    /**
     * get available statuses.
     *
     * @return []
     */
    public function toOptionArray() {
        return [
            self::STATUS_ENABLED => __('Enabled')
            , self::STATUS_DISABLED => __('Disabled'),
        ];
    }

}
