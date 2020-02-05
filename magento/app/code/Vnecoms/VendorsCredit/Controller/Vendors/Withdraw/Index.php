<?php

namespace Vnecoms\VendorsCredit\Controller\Vendors\Withdraw;

class Index extends \Vnecoms\Vendors\Controller\Vendors\Action
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
        $this->_coreRegistry->register('step', 'select_method');
        $this->_initAction();
        $title = $this->_view->getPage()->getConfig()->getTitle();
        $title->prepend(__("Credit"));
        $title->prepend(__("Withdraw Funds"));
        $this->_view->renderLayout();
    }
}
