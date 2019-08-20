<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Water.
 */
class Water implements ArrayInterface
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
                'value' => '',
                'label' => __('None'),
            ],
            [
                'value' => '1',
                'label' => __('Image'),
            ],
            [
                'value' => '2',
                'label' => __('Text Only'),
            ],

        ];

        return $_options;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getOptions(array $options = [])
    {
        $_tmpOptions = $this->toOptionArray($options);
        $_options = [];
        foreach ($_tmpOptions as $option) {
            $_options[$option['value']] = $option['label'];
        }

        return $_options;
    }
}
