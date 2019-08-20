<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Attach.
 */
class Attach implements ArrayInterface
{
    // For simply process attachments, we only use yes or no attachment
    // If admin not setup email he can't receive attachment pdf
    const ATTACH_TYPE_NO = 0;
    const ATTACH_TYPE_YES = 1;
    /*const ATTACH_TYPE_BOTH = 1;
    const ATTACH_TYPE_CUSTOMER = 2;
    const ATTACH_TYPE_ADMIN = 3;*/

    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_options = [
            [
                'value' => self::ATTACH_TYPE_NO,
                'label' => __('No'),
            ],
            [
                'value' => self::ATTACH_TYPE_YES,
                'label' => __('Yes'),
            ]
//            [
//                'value' => self::ATTACH_TYPE_CUSTOMER,
//                'label' => __('Customer'),
//            ],
//            [
//                'value' => self::ATTACH_TYPE_ADMIN,
//                'label' => __('Admin'),
//            ],
//            [
//                'value' => self::ATTACH_TYPE_BOTH,
//                'label' => __('Both'),
//            ],
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
