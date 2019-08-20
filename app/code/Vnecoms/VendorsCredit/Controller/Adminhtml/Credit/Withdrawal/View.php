<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Controller\Adminhtml\Credit\Withdrawal;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class View extends Action
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
        if (!$id || !$withdrawal->getId()) {
            $this->messageManager->addError(__("The withdrawal request is not exist."));
            $back = $this->getRequest()->getParam('back', '');
            return $this->_redirect('*/*/'.$back);
        }
        
        $this->_coreRegistry->register('withdrawal', $withdrawal);
        $this->_coreRegistry->register('current_withdrawal', $withdrawal);
        $this->_initAction()->_addBreadcrumb(__('View Withdrawal'), __('View Withdrawal'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('View Withdrawal'));
        $this->_view->renderLayout();
    }
}
