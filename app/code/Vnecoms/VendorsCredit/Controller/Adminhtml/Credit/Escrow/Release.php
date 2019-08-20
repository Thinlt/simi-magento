<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Controller\Adminhtml\Credit\Escrow;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class Release extends Action
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
            $this->messageManager->addError(__("The pending transaction is not exist."));
            return $this->_redirect('vendors/credit/pending');
        }
        
        try {
            $escrow->release();
            $this->messageManager->addSuccess(__("The pending credit is released."));
            return $this->_redirect('vendors/credit_escrow/view', ['id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $this->_redirect('vendors/credit/pending');
    }
}
