<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Controller\Adminhtml\Credit\Withdrawal;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class Pending extends Action
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_VendorsCredit::credit_all_withdrawal');
    }
    

    /**
     * @return void
     */
    public function execute()
    {
        $this->getRequest()->setParam('status', \Vnecoms\VendorsCredit\Model\Withdrawal::STATUS_PENDING);
        $this->_initAction()->_addBreadcrumb(__('Credit'), __('Credit'))->_addBreadcrumb(__('Pending Withdrawal Reqests'), __('Pending Withdrawal Reqests'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Pending Withdrawal Reqests'));
        $this->_view->renderLayout();
    }
}
