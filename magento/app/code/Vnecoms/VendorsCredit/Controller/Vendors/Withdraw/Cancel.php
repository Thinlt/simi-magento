<?php

namespace Vnecoms\VendorsCredit\Controller\Vendors\Withdraw;

use Vnecoms\VendorsCredit\Model\CreditProcessor\CancelWithdrawal;

class Cancel extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsCredit::credit_withdraw';
    
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id', false);
        $withdrawal = $this->_objectManager->create('Vnecoms\VendorsCredit\Model\Withdrawal');
        $withdrawal->load($id);
        try {
            $vendor = $this->_session->getVendor();
            if (!$id || !$withdrawal->getId() || ($withdrawal->getVendorId() != $vendor->getId())) {
                $this->messageManager->addError(__("The withdrawal request is not exist."));
                $back = $this->getRequest()->getParam('back', '');
                return $this->_redirect('*/*/'.$back);
            }
            
            $withdrawal->cancel();
            
            /*Send cancel withdrawal request notification email*/
            /**
             * @var \Vnecoms\VendorsCredit\Helper\Data
             */
            $vendorCreditHelper = $this->_objectManager->create('Vnecoms\VendorsCredit\Helper\Data');
            $vendorCreditHelper->sendWithdrawalCancelledNotification($withdrawal, $this->_session->getVendor());

            /*Return Credit*/
            /*Create transaction to subtract the credit.*/
            
            $creditAccount = $this->_objectManager->create('Vnecoms\Credit\Model\Credit');
            $creditAccount->loadByCustomerId($this->_session->getCustomer()->getId());
            
            $data = [
                'vendor' => $this->_session->getVendor(),
                'type' => CancelWithdrawal::TYPE,
                'amount' => $withdrawal->getAmount(),
                'withdrawal_request' => $withdrawal,
            ];
            
            $creditProcessor = $this->_objectManager->create('Vnecoms\Credit\Model\Processor');
            $creditProcessor->process($creditAccount, $data);
            
            $this->messageManager->addSuccess(__("The withdrawal request has been canceled."));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $this->_redirect('*/*/history');
    }
}
