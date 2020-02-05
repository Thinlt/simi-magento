<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Lang.
 */
class Lang implements ArrayInterface
{
    const CORE = 1;
    const ALL = 2;
    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_options = [
            [
                'value' => self::CORE,
                'label' => __('Use Core Fonts'),
            ],
            [
                'value' => self::ALL,
                'label' => __('Automatic Detect All Languages'),
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
