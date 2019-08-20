<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Controller\Adminhtml\Account;

use Vnecoms\Credit\Controller\Adminhtml\Action;
use Magento\Customer\Controller\RegistryConstants;

class Grid extends Action
{

    /**
     * @return void
     */
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('id',0);
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
        
        $grid = $this->_view->getLayout()->createBlock('Vnecoms\Credit\Block\Adminhtml\Customer\Edit\Tab\Credit\Transaction\Grid');
        $this->getResponse()->setBody($grid->toHtml());
    }
}
