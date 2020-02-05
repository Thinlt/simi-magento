<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Controller\Adminhtml\Product;

use Vnecoms\Credit\Controller\Adminhtml\Action;

class Index extends Action
{

    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Manage Credit Products'), __('Manage Credit Products'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Credit Products'));
        $this->_view->renderLayout();
    }
}
