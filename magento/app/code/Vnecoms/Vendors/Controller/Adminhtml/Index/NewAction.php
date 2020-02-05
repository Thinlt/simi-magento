<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Controller\Adminhtml\Index;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class NewAction extends Action
{
    /**
     * Create new customer action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {

        $vendor = $this->_objectManager->create('Vnecoms\Vendors\Model\Vendor');
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getCustomerFormData(true);
        if (!empty($data)) {
            $vendorData = $data["vendor_data"];
            unset($data["vendor_data"]);
            $vendor->addData($vendorData);
            $customer->addData($data);
        }
        $this->_coreRegistry->register('current_vendor', $vendor);
        $this->_coreRegistry->register('current_customer', $customer);

        $this->_initAction()->_addBreadcrumb(__('Sellers'), __('Sellers'))
            ->_addBreadcrumb(__('Manage Sellers'), __('Manage Sellers'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Seller'));
        $this->_view->renderLayout();
    }
}
