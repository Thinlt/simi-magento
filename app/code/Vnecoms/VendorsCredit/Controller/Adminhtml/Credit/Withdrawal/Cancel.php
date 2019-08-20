<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Controller\Adminhtml\Credit\Withdrawal;

use Vnecoms\Vendors\Controller\Adminhtml\Action;
use Vnecoms\VendorsCredit\Model\CreditProcessor\CancelWithdrawal;

class Cancel extends Action
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_VendorsCredit::credit');
    }

    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id', false);
        $withdrawal = $this->_objectManager->create('Vnecoms\VendorsCredit\Model\Withdrawal');
        $withdrawal->load($id);
        try {
            if (!$id || !$withdrawal->getId()) {
                $this->messageManager->addError(__("The withdrawal request is not exist."));
                $back = $this->getRequest()->getParam('back', '');
                return $this->_redirect('*/*/'.$back);
            }
            $reasonCancel = $this->getRequest()->getParam('reason_cancel', false);
            $withdrawal->setReasonCancel($reasonCancel);

            $withdrawal->cancel();
            
            $vendor = $this->_objectManager->create('Vnecoms\Vendors\Model\Vendor');
            $vendor->load($withdrawal->getVendorId());
            
            /*Send cancel withdrawal request notification email*/
            $vendorCreditHelper = $this->_objectManager->create('Vnecoms\VendorsCredit\Helper\Data');
            $vendorCreditHelper->sendWithdrawalCancelledNotification($withdrawal, $vendor);
            
            /*Return Credit*/
            /*Create transaction to subtract the credit.*/
            
            
            $relatedCustomerId = $vendor->getResource()->getRelatedCustomerIdByVendorId($vendor->getId());
            $creditAccount = $this->_objectManager->create('Vnecoms\Credit\Model\Credit');
            $creditAccount->loadByCustomerId($relatedCustomerId);
            
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
        return $this->_redirect('*/*/');
    }
}
