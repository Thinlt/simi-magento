<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Controller\Adminhtml\Credit\Escrow;

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
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_VendorsCredit::credit_pending');
    }

    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id', false);
        $escrow = $this->_objectManager->create('Vnecoms\VendorsCredit\Model\Escrow');
        $escrow->load($id);
        if (!$id || !$escrow->getId()) {
            $this->messageManager->addError(__("The escrow transaction is not exist."));
            return $this->_redirect('vendors/credit/pending');
        }
        
        $this->_coreRegistry->register('escrow', $escrow);
        $this->_coreRegistry->register('current_escrow', $escrow);
        $this->_initAction()->_addBreadcrumb(__('View Escrow Transaction'), __('View Escrow Transaction'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('View Escrow Transaction'));
        $this->_view->renderLayout();
    }
}
