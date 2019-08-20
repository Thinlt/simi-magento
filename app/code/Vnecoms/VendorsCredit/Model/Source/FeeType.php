<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Model\Source;

use Vnecoms\VendorsCredit\Model\Withdrawal\Method\AbstractMethod;

class FeeType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

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
    public function getAllOptions($blankOption = true)
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __("Fixed"), 'value' => AbstractMethod::FEE_TYPE_FIXED],
                ['label' => __("Percent"), 'value' => AbstractMethod::FEE_TYPE_PERCENT],
            ];
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray($blankOption = true)
    {
        $_options = [];
        foreach ($this->getAllOptions($blankOption) as $option) {
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
