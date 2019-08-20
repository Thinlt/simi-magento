<?php

namespace Vnecoms\VendorsCredit\Model\Source;

use Vnecoms\VendorsCredit\Model\Withdrawal;

class WithdrawalMethod extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Vnecoms\VendorsCredit\Helper\Data
     */
    protected $helper;
    
    public function __construct(
        \Vnecoms\VendorsCredit\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    
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
            $this->_options = [];
            $availableMethods = $this->helper->getWithdrawalMethods();
            foreach ($availableMethods as $code => $method) {
                $this->_options[] = ['label' => $method->getTitle(), 'value' => $code];
            }
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
