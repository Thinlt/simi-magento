<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Orientation.
 */
class Orientation implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            'P' => __('Portrait'),
            'L' => __('Landscape'),
        );
    }
}
