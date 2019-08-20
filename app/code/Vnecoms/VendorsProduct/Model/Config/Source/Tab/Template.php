<?php

namespace Vnecoms\VendorsProduct\Model\Config\Source\Tab;

class Template implements \Magento\Framework\Option\ArrayInterface
{
    const TEMPLATE_VERTICAL_TABS    = 'vertical';
    const TEMPLATE_HORIZONTAL_TABS  = 'horizontal';
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TEMPLATE_VERTICAL_TABS, 'label' => __('Vertical Tabs')],
            /* ['value' => self::TEMPLATE_HORIZONTAL_TABS, 'label' => __('Horizontal Tabs')], */
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::TEMPLATE_VERTICAL_TABS    => __('Vertical Tabs'),
            /* self::TEMPLATE_HORIZONTAL_TABS  => __('Horizontal Tabs') */
        ];
    }
}
