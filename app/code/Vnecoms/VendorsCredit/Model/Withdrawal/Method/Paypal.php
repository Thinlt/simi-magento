<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Model\Withdrawal\Method;

class Paypal extends AbstractMethod
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Vnecoms\VendorsCredit\Model\Withdrawal\Method\Context $context
    ) {
        parent::__construct($context);
        $this->_code = 'paypal';
    }
    
    /**
     * @see \Vnecoms\VendorsCredit\Model\Withdrawal\Method\AbstractMethod::getBlock()
     */
    public function getBlock()
    {
        return 'Vnecoms\VendorsCredit\Block\Vendors\Withdraw\Method\Paypal';
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsCredit\Model\Withdrawal\Method\AbstractMethod::isEnteredMethodInfo()
     */
    public function isEnteredMethodInfo($vendorId)
    {
        if ($vendorId instanceof \Vnecoms\Vendors\Model\Vendor) {
            $vendorId = $vendorId->getId();
        }
        
        return strlen($this->getPaypalEmailAccount($vendorId));
    }
    
    /**
     * Get Paypal email account
     *
     * @param unknown $vendorId
     */
    public function getPaypalEmailAccount($vendorId)
    {
        return $this->_vendorConfigHelper->getVendorConfig(
            'withdrawal/paypal/email',
            $vendorId
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsCredit\Model\Withdrawal\Method\AbstractMethod::getVendorAccountInfo()
     */
    public function getVendorAccountInfo($vendorId)
    {
        $info = [
            ['label' => 'Paypal Email Account', 'value' => $this->getPaypalEmailAccount($vendorId)]
        ];
        
        return $info;
    }
}
