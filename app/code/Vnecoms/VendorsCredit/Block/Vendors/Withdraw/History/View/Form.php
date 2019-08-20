<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Block\Vendors\Withdraw\History\View;

use Vnecoms\VendorsCredit\Model\Withdrawal;
use Vnecoms\VendorsCredit\Model\CreditProcessor\Withdraw;

class Form extends \Vnecoms\Vendors\Block\Vendors\AbstractBlock
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'withdraw/history/view.phtml';
    
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     * @var \Vnecoms\VendorsCredit\Model\Source\Status
     */
    protected $_withdrawalStatus;
    
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Vnecoms\Vendors\Model\UrlInterface $url,
        \Vnecoms\Vendors\Model\Session $session,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Vnecoms\VendorsCredit\Model\Source\Status $withdrawalStatus,
        array $data = []
    ) {
        parent::__construct($context, $url, $data);
        $this->_vendorSession = $session;
        $this->_coreRegistry = $coreRegistry;
        $this->_objectManager = $objectManager;
        $this->_withdrawalStatus = $withdrawalStatus;
    }
    

    public function getPaymentMethodTitle()
    {
        return $this->getWithdrawal()->getMethodTitle();
    }
    
    /**
     * Get current withdrawal
     *
     * @return \Vnecoms\VendorsCredit\Model\Withdrawal
     */
    public function getWithdrawal()
    {
        return $this->_coreRegistry->registry('current_withdrawal');
    }
    
    /**
     * Format base currency
     *
     * @param float $amount
     * @return string
     */
    public function formatBaseCurrency($amount)
    {
        return $this->_storeManager->getStore()->getBaseCurrency()
        ->formatPrecision($amount, 2, [], false);
    }
    /**
     * Get Additional Info
     *
     * @return array
     */
    public function getAdditionalInfo()
    {
        $additionalInfo = json_decode($this->getWithdrawal()->getAdditionalInfo(), true);
        return $additionalInfo?$additionalInfo:[];
    }
    
    /**
     * Get Withdrawal Status Label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $status = $this->_withdrawalStatus->getOptionArray();
        return $status[$this->getWithdrawal()->getStatus()];
    }

    /**
     * get code of transfer
     * @return mixed
     */
    public function getCodeOfTransfer(){
        $code = $this->getWithdrawal()->getCodeOfTransfer();
        return $code;
    }

    /**
     * get reason cancel
     * @return mixed
     */
    public function getReasonCancel(){
        $code = $this->getWithdrawal()->getReasonCancel();
        return $code;
    }

    /**
     * Get status html class
     *
     * @return string
     */
    public function getStatusHtmlClass()
    {
        switch ($this->getWithdrawal()->getStatus()) {
            case Withdrawal::STATUS_PENDING:
                return 'label-warning';
            case Withdrawal::STATUS_COMPLETED:
                return 'label-success';
            case Withdrawal::STATUS_CANCELED:
                return 'label-default';
            /* case Withdrawal::STATUS_REJECTED:
                return 'label-danger'; */
        }
    }
}
