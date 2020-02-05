<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Controller\Adminhtml\Account;

use Vnecoms\Credit\Controller\Adminhtml\Action;

class Index extends Action
{

    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Manage Credit Accounts'), __('Manage Credit Accounts'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Credit Accounts'));
        $this->_view->renderLayout();
    }
}
