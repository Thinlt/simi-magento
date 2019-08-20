<?php
namespace Vnecoms\VendorsCustomWithdrawal\Model\Source;

use Vnecoms\VendorsCustomWithdrawal\Model\Method;

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
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Fixed'), 'value' => Method::FEE_TYPE_FIXED],
                ['label' => __('Percent'), 'value' => Method::FEE_TYPE_PERCENT],
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
