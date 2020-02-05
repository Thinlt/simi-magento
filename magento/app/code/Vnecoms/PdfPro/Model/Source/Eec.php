<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Eec.
 */
class Eec implements ArrayInterface
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
                'value' => 'low',
                'label' => __('L - Smallest'),
            ],
            [
                'value' => 'medium',
                'label' => __('M'),
            ],
            [
                'value' => 'quartile',
                'label' => __('Q'),
            ],
            [
                'value' => 'high',
                'label' => __('H - Best'),
            ],
        ];

        return $_options;
    }

    //TODO move this in parent class
    /**
     * get options as key value pair.
     *
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
