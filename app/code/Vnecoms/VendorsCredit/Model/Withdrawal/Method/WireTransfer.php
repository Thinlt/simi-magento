<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Model\Withdrawal\Method;

class WireTransfer extends AbstractMethod
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
        $this->_code = 'wire_transfer';
    }
    
    /**
     * @see \Vnecoms\VendorsCredit\Model\Withdrawal\Method\AbstractMethod::getBlock()
     */
    public function getBlock()
    {
        return 'Vnecoms\VendorsCredit\Block\Vendors\Withdraw\Method\WireTransfer';
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
        
        $bankName = $this->getBankName($vendorId);
        $swiftCode = $this->getSwiftCode($vendorId);
        $accountHolder = $this->getAccountHolder($vendorId);
        $accountNumber = $this->getAccountNumber($vendorId);

        return strlen($bankName) && strlen($swiftCode) && strlen($accountHolder) && strlen($accountNumber);
    }
    
    /**
     * Get Bank Name
     *
     * @param int $vendorId
     */
    public function getBankName($vendorId)
    {
        return $this->_vendorConfigHelper->getVendorConfig(
            'withdrawal/wire_transfer/bank_name',
            $vendorId
        );
    }
    
    /**
     * Get SWIFT Code
     *
     * @param int $vendorId
     */
    public function getSwiftCode($vendorId)
    {
        return $this->_vendorConfigHelper->getVendorConfig(
            'withdrawal/wire_transfer/swift_code',
            $vendorId
        );
    }
    
    /**
     * Get Account Holder
     *
     * @param int $vendorId
     */
    public function getAccountHolder($vendorId)
    {
        return $this->_vendorConfigHelper->getVendorConfig(
            'withdrawal/wire_transfer/account_holder',
            $vendorId
        );
    }
    
    /**
     * Get Account Number
     *
     * @param int $vendorId
     */
    public function getAccountNumber($vendorId)
    {
        return $this->_vendorConfigHelper->getVendorConfig(
            'withdrawal/wire_transfer/account_number',
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
            ['label' => 'Bank Name', 'value' => $this->getBankName($vendorId)],
            ['label' => 'SWIFT Code', 'value' => $this->getSwiftCode($vendorId)],
            ['label' => 'Account Holder', 'value' => $this->getAccountHolder($vendorId)],
            ['label' => 'Account Number', 'value' => $this->getAccountNumber($vendorId)],
        ];
    
        return $info;
    }
}
