<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Size.
 */
class Size implements ArrayInterface
{
    /**
     * @var array
     */
    public static $PAPER_SIZES = array(
        'A0' => 'A0',
        'A1' => 'A1',
        'A2' => 'A2',
        'A3' => 'A3',
        'A4' => 'A4',
        'A5' => 'A5',
        'A6' => 'A6',
        'A7' => 'A7',
        'A8' => 'A8',
        'A9' => 'A9',
        'A10' => 'A10',
        'letter' => 'Letter',
    );

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return self::$PAPER_SIZES;
    }
}
