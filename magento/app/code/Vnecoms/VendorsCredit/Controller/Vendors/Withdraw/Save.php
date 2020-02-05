<?php

namespace Vnecoms\VendorsCredit\Controller\Vendors\Withdraw;

use Vnecoms\VendorsCredit\Model\Withdrawal as WithdrawalModel;
use Vnecoms\VendorsCredit\Model\CreditProcessor\Withdraw as WithdrawProcessor;

class Save extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsCredit::credit_withdraw';
    
    /**
     * @var \Vnecoms\VendorsCredit\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Vnecoms\Credit\Model\CreditFactory
     */
    protected $_withdrawalFactory;
    
    /**
     * @var \Vnecoms\Credit\Model\Processor
     */
    protected $_creditProcessor;
    
    /**
     * @var \Vnecoms\Credit\Model\CreditFactory
     */
    protected $_creditAccountFactory;
    
    /**
     * @var \Vnecoms\VendorsCredit\Helper\Data
     */
    protected $_vendorCreditHelper;
    
    /**
     * @param \Vnecoms\Vendors\App\Action\Context $context
     * @param \Vnecoms\VendorsCredit\Helper\Data $helper
     * @param \Vnecoms\Credit\Model\Processor $creditProcessor
     * @param \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory
     * @param \Vnecoms\VendorsCredit\Model\WithdrawalFactory $withdrawalFactory
     * @param \Vnecoms\VendorsCredit\Helper\Data $vendorCreditHelper
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context,
        \Vnecoms\VendorsCredit\Helper\Data $helper,
        \Vnecoms\Credit\Model\Processor $creditProcessor,
        \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory,
        \Vnecoms\VendorsCredit\Model\WithdrawalFactory $withdrawalFactory,
        \Vnecoms\VendorsCredit\Helper\Data $vendorCreditHelper
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->_withdrawalFactory = $withdrawalFactory;
        $this->_creditProcessor = $creditProcessor;
        $this->_creditAccountFactory = $creditAccountFactory;
        $this->_vendorCreditHelper = $vendorCreditHelper;
    }

    
    /**
     * @return void
     */
    public function execute()
    {
        $params  = $this->_session->getWithdrawalParams();
        try {
            if (!isset($params['method']) || !isset($params['amount'])) {
                throw new \Exception(__('The params is not valid.'));
            }
            
            $methodCode = $params['method'];
            
            $vendorId = $this->_session->getVendor()->getId();
            $availableMethods = $this->helper->getWithdrawalMethods();
            $method = $availableMethods[$methodCode];
            $amount = $params['amount'];
            $fee = $method->calculateFee($amount);
            $netAmount = $amount - $fee;
            
            $withdrawal = $this->_withdrawalFactory->create();
            $additionalInfo = $this->_session->getData('withdrawal_params');
            $additionalInfo = isset($additionalInfo['additional_info'])?$additionalInfo['additional_info']:[];
            $additionalInfo = array_merge($additionalInfo, $method->getVendorAccountInfo($vendorId));
            $withdrawal->setData([
                'vendor_id' => $vendorId,
                'method' => $methodCode,
                'method_title' => $method->getTitle(),
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $netAmount,
                'additional_info' => json_encode($additionalInfo),
                'status' => WithdrawalModel::STATUS_PENDING,
            ]);

            $withdrawal->save();
            
            /*Send new withdrawal notification email to vendor*/
            $this->_vendorCreditHelper->sendNewWithdrawalNotificationToVendor($withdrawal, $this->_session->getVendor());
            
            /*Send new withdrawal notification email to admin*/
            $this->_vendorCreditHelper->sendNewWithdrawalNotificationToAdmin($withdrawal, $this->_session->getVendor());
            
            /*Reset session*/
            $this->_session->setWithdrawalParams(null);
                        
            /*Create transaction to subtract the credit.*/
            $creditAccount = $this->_creditAccountFactory->create();
            $creditAccount->loadByCustomerId($this->_session->getCustomerId());
            $data = [
                'vendor' => $this->_session->getVendor(),
                'type' => WithdrawProcessor::TYPE,
                'amount' => $amount,
                'withdrawal_request' => $withdrawal,
            ];
            $this->_creditProcessor->process($creditAccount, $data);
            
            /* Send notification email here */
            
            $this->messageManager->addSuccess(__("Your withdrawal request has been submited."));
            return $this->_redirect('credit/withdraw/history');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->_redirect('*/*');
        }
    }
}
