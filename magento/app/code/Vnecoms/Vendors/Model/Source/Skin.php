<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\Source;

class Skin extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    const SKIN_BLUE         = 'blue';
    const SKIN_BLACK        = 'black';
    const SKIN_PURPLE       = 'purple';
    const SKIN_GREEN        = 'green';
    const SKIN_RED          = 'red';
    const SKIN_YELLOW      = 'yellow';
    const SKIN_BLUE_LIGHT   = 'blue_light';
    const SKIN_BLACK_LIGHT  = 'black_light';
    const SKIN_PURPLE_LIGHT = 'purple_light';
    const SKIN_GREEN_LIGHT  = 'green_light';
    const SKIN_RED_LIGHT    = 'red_light';
    const SKIN_YELLOW_LIGHT = 'yellow_light';
    
    /**
     * Options array
     *
     * @var array
     */
    protected $_options = null;
    
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Blue'), 'value' => self::SKIN_BLUE],
                ['label' => __('Black'), 'value' => self::SKIN_BLACK],
                ['label' => __('Purple'), 'value' => self::SKIN_PURPLE],
                ['label' => __('Green'), 'value' => self::SKIN_GREEN],
                ['label' => __('Red'), 'value' => self::SKIN_RED],
                ['label' => __('Yellow'), 'value' => self::SKIN_YELLOW],
                ['label' => __('Blue Light'), 'value' => self::SKIN_BLUE_LIGHT],
                ['label' => __('Black Light'), 'value' => self::SKIN_BLACK_LIGHT],
                ['label' => __('Purple Light'), 'value' => self::SKIN_PURPLE_LIGHT],
                ['label' => __('Green Light'), 'value' => self::SKIN_GREEN_LIGHT],
                ['label' => __('Red Light'), 'value' => self::SKIN_RED_LIGHT],
                ['label' => __('Yellow Light'), 'value' => self::SKIN_YELLOW_LIGHT],
            ];
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
    
    
    /**
     * Get options as array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
