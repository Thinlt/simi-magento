<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Controller\Vendors\Index;

class Index extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_Vendors::account';
    
    /**
     * @return void
     */
    public function execute()
    {
        $key = $this->getRequest()->getModuleName()."_".$this->getRequest()->getActionName();
        if ($key == 'vendors_index') {
            $this->_redirectUrl($this->_backendUrl->getStartupPageUrl());
            return;
        }
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__("Account"));
        $this->_addBreadcrumb(__("Account"), __("Account"));
        $vendor = $this->_session->getVendor();
        $this->_coreRegistry->register('current_vendor', $vendor);
        $this->_view->renderLayout();
    }
    
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        $key = $this->getRequest()->getModuleName()."_".$this->getRequest()->getActionName();
        if ($key == 'vendors_index') return true;
        return parent::_isAllowed();
    }
}
