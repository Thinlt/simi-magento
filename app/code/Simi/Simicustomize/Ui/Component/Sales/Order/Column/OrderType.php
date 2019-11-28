<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Ui\Component\Sales\Order\Column;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class OrderType implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    const ORDER_TYPE_PRE_ORDER_WAITING = 'pre_order_waiting';
    const ORDER_TYPE_PRE_ORDER_PAID = 'pre_order_paid';
    const ORDER_TYPE_PRE_ORDER_2ND_ORDER = 'pre_order_2nd_order';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $options = [
                ['label' => __('No type'),      'value' => ''],
                ['label' => __('Try to buy'),   'value' => 'try_to_buy'],
                ['label' => __('Pre-order Waiting'),    'value' => self::ORDER_TYPE_PRE_ORDER_WAITING],
                ['label' => __('Pre-order Paid'),    'value' => self::ORDER_TYPE_PRE_ORDER_PAID],
                ['label' => __('Pre-order 2nd Order'),    'value' => self::ORDER_TYPE_PRE_ORDER_2ND_ORDER],
                ['label' => __('Other'),        'value' => 'other'],
            ];
            array_walk($options, function (&$option) {
                $option['__disableTmpl'] = true;
            });
            $this->options = $options;
        }
        return $this->options;
    }
}
