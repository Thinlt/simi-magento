<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\Source;

class PanelType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    const TYPE_SIMPLE = 0;
    const TYPE_ADVANCED = 1;
    
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
                /* ['label' => __('Use Frontend Theme'), 'value' => self::TYPE_SIMPLE], */
                ['label' => __('Use Separated Seller Panel'), 'value' => self::TYPE_ADVANCED],
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
