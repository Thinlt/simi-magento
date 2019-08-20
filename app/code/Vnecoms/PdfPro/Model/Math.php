<?php

namespace Vnecoms\PdfPro\Model;

/**
 * Class Math.
 */
class Math extends \Magento\Framework\DataObject
{
    /**
     * Math constructor.
     *
     * @param array $data
     */
    public function __construct(
        array $data = []
    ) {
        parent::__construct($data);
    }

    /**
     * @param $a
     * @param $b
     * @param $operator
     *
     * @return float|int
     */
    final public static function compare($a, $b, $operator)
    {
        switch ($operator) {
            case '==':
                return $a == $b;
            case '===':
                return $a === $b;
            case '!=':
                return $a != $b;
            case '<>':
                return $a != $b;
            case '!==':
                return $a !== $b;
            case '>':
                return $a > $b;
            case '<':
                return $a < $b;
            case '>=':
                return $a >= $b;
            case '<=':
                return $a <= $b;
            case '+':
                return $a + $b;
            case '-':
                return $a - $b;
            case '*':
                return $a * $b;
            case '/':
                return $a / $b;
            case '%':
                return $a % $b;
        }
    }
}
