<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Currency.
 */
class Currency implements ArrayInterface
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
                'value' => \Zend_Currency::STANDARD,
                'label' => __('Standard'),
            ],
            [
                'value' => \Zend_Currency::LEFT,
                'label' => __('Customer'),
            ],
            [
                'value' => \Zend_Currency::RIGHT,
                'label' => __('Admin'),
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
