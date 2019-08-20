<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Model\Withdrawal\Method;

/**
 * Withdrawl Method
 */
abstract class AbstractMethod
{
    const FEE_TYPE_FIXED = 'fixed';
    const FEE_TYPE_PERCENT = 'percent';
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_vendorConfigHelper;
    
    /**
     * Method Code
     *
     * @var string
     */
    protected $_code;
    
    /**
     * Constructor
     *
     * @param \Vnecoms\VendorsCredit\Model\Withdrawal\Method\Context $context
     */
    public function __construct(
        \Vnecoms\VendorsCredit\Model\Withdrawal\Method\Context $context
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_vendorConfigHelper = $context->getVendorConfigHelper();
    }
    
    /**
     * Get Method Code
     *
     * @return string
     */
    public function getCode()
    {
        if (!$this->_code) {
            throw new \Exception(__("Method code of '%1' is not defined.", __CLASS__));
        }
        return $this->_code;
    }
    
    /**
     * Is Active
     *
     * @return string
     */
    public function isActive()
    {
        return $this->_scopeConfig->getValue('withdrawal_methods/'.$this->getCode().'/active');
    }
    
    /**
     * Get Method Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_scopeConfig->getValue('withdrawal_methods/'.$this->getCode().'/title');
    }
    
    /**
     * Get Method Description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_scopeConfig->getValue('withdrawal_methods/'.$this->getCode().'/description');
    }
    
    /**
     * Fee Type
     *
     * @return Ambigous <string, \Magento\Framework\App\Config\mixed>
     */
    public function getFeeType()
    {
        $feeType = $this->_scopeConfig->getValue('withdrawal_methods/'.$this->getCode().'/fee_type');
        if (!$feeType ||
            !in_array($feeType, [self::FEE_TYPE_FIXED, self::FEE_TYPE_PERCENT])
        ) {
            $feeType = 'fixed';
        }
        
        return $feeType;
    }
    
    /**
     * Get Method Fee
     *
     * @return string
     */
    public function getFee($isFormated = true)
    {
        $fee = $this->_scopeConfig->getValue('withdrawal_methods/'.$this->getCode().'/fee');
        if ($this->getFeeType() == self::FEE_TYPE_PERCENT) {
            return $isFormated?__('%1%', $fee):$fee;
        }
        
        return $isFormated?$this->_storeManager->getStore()->getBaseCurrency()->formatPrecision($fee, 2, [], false):$fee;
    }
    
    /**
     * Calculate Fee
     *
     * @param number $amount
     * @return number
     */
    public function calculateFee($amount = 0)
    {
        if ($this->getFeeType() == self::FEE_TYPE_PERCENT) {
            return $amount * $this->getFee(false) / 100;
        }
        return $this->getFee(false);
    }
    
    /**
     * Get Method Max Value
     *
     * @return string
     */
    public function getMaxValue()
    {
        return (float)$this->_scopeConfig->getValue('withdrawal_methods/'.$this->getCode().'/max_value');
    }
    
    /**
     * Get Method Max Value
     *
     * @return string
     */
    public function getMinValue()
    {
        return (float)$this->_scopeConfig->getValue('withdrawal_methods/'.$this->getCode().'/min_value');
    }
    
    /**
     * Get Method Block Name
     *
     * @return string
     */
    abstract public function getBlock();
    
    /**
     * Check if the vendor's info is set for the withdrawal method
     *
     * @param int $vendorId
     * @return boolean
     */
    abstract public function isEnteredMethodInfo($vendorId);
    
    /**
     * Get the withdrawal account information of the vendor
     *
     * @param int $vendorId
     * @return array
     */
    abstract public function getVendorAccountInfo($vendorId);
}
