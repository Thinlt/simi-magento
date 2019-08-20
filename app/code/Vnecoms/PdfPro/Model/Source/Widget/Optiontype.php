<?php

namespace Vnecoms\PdfPro\Model\Source\Widget;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Optiontype.
 */
class Optiontype implements ArrayInterface
{
    const OPTION_TEXT = 'text';
    const OPTION_IMAGE = 'image';

    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            self::OPTION_TEXT => array('code' => 'text', 'title' => __('Default')),
            self::OPTION_IMAGE => array('code' => 'image', 'title' => __('Image')),
        );
    }
}
