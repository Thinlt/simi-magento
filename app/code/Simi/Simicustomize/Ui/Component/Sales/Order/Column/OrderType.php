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
                ['label' => __('Pre-order'),    'value' => 'pre_order'],
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
