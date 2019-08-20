<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCommission\Controller\Adminhtml\Commission;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class Index extends Action
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_VendorsCommission::commission_configuration');
    }
    

    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Sellers'), __('Sellers'))->_addBreadcrumb(__('Manage Commission Rules'), __('Manage  Commission Rules'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage  Commission Rules'));
        $this->_view->renderLayout();
    }
}
