<?php
namespace Vnecoms\VendorsProfileNotification\Controller\Adminhtml\Profile\Process;

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
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_VendorsProfileNotification::manage_process');
    }
    
    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Sellers'), __('Sellers'))->_addBreadcrumb(__('Manage Profile Processes'), __('Manage Profile Processes'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Profile Processes'));
        $this->_view->renderLayout();
    }
}
