<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\Source\Product;

use Magento\Framework\DB\Ddl\Table;

class Flag extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    const STATUS_NOT_REQUIRED   = 0;
    const STATUS_REQUIRED       = 1;
    /**4
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
                ['label' => __('All selected attributes will be required for approval'), 'value' => self::STATUS_REQUIRED],
                ['label' => __('All selected attributes will not be required for approval.'), 'value' => self::STATUS_NOT_REQUIRED],
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
