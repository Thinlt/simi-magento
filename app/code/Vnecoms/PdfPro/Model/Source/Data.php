<?php

namespace Vnecoms\PdfPro\Model\Source;

/**
 * Class Data.
 */
class Data extends \Magento\Framework\DataObject
{
    const FONT_FAMILY = 1;
    const FONT_SIZE = 2;
    const FONT_ITALIC = 3;
    const FONT_BOLD = 4;
    /**
     * Get options.
     *
     * @param int $type
     *
     * @return array
     */
    public static function toOptionArray($type)
    {
        switch ($type) {
            case self::FONT_FAMILY:
                return array(
                    array('label' => __('San-serif'), 'value' => 'san-serif'),
                    array('label' => __('Serif'), 'value' => ' serif'),
                );
                break;
            case self::FONT_ITALIC:
                return array(
                    array('label' => __('No'), 'value' => '0'),
                    array('label' => __('Yes'), 'value' => '1'),
                );
                break;
            case self::FONT_BOLD:
                return array(
                    array('label' => __('No'), 'value' => '0'),
                    array('label' => __('Yes'), 'value' => '1'),
                );
                break;
            case self::FONT_SIZE:
                $return = array();
                foreach (array('8', '10', '12', '14', '16', '18', '24', '32', '48') as $_size) {
                    $return[] = array('label' => __($_size), 'value' => $_size);
                }

                return $return;
                break;
        }
    }
}
