<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Qrsize.
 */
class Qrsize implements ArrayInterface
{
    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $array = array();
        for ($i = 1; $i <= 10; ++$i) {
            $data['label'] = $i;
            $data['value'] = $i;
            $array[] = $data;
        }

        return $array;
    }
}
