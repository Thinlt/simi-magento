<?php

namespace Vnecoms\VendorsSales\Controller\Vendors\Order;

class Index extends \Vnecoms\VendorsSales\Controller\Vendors\Order
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsSales::sales_orders';
    
    /**
     * @return void
     */
    public function execute()
    {
        $this->getRequest()->setParam('vendor_id', $this->_session->getVendor()->getId());
        $this->_initAction();
        $this->setActiveMenu('Vnecoms_VendorsSales::sales_orders');
        $title = $this->_view->getPage()->getConfig()->getTitle();
        $title->prepend(__("Sales"));
        $title->prepend(__("Orders"));
        $this->_view->renderLayout();
    }
}
