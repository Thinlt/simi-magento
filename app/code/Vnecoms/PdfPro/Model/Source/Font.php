<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Font.
 */
class Font implements ArrayInterface
{
    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_options = [
            [
                'value' => '0',
                'label' => __('No Label'),
            ],
            [
                'value' => 'Arial.ttf',
                'label' => __('Arial'),
            ],
        ];

        return $_options;
    }
}
