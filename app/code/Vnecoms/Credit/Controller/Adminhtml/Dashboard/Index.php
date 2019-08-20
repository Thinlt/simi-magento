<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Controller\Adminhtml\Dashboard;

use Vnecoms\Credit\Controller\Adminhtml\Action;

class Index extends Action
{

    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Credit Dashboard'), __('Credit Dashboard'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Credit Dashboard'));
        $this->_view->renderLayout();
    }
}
