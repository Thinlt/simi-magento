<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Rotation.
 */
class Rotation implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '0' => __('No rotation'),
            '90' => __('90 degree clockwise'),
            '180' => __('180 degree clockwise'),
            '270' => __('270 degree clockwise'),
        );
    }
}
