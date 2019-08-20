<?php

namespace Vnecoms\VendorsSales\Controller\Vendors\Order;

class View extends \Vnecoms\VendorsSales\Controller\Vendors\Order
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsSales::sales_order_action_view';
    
    /**
     * View order detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $order = $this->_initOrder();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($order) {
            try {
                $this->_initAction();
                $this->setActiveMenu('Vnecoms_VendorsSales::sales_orders');
                $title = $this->_view->getPage()->getConfig()->getTitle();
                $title->prepend(__("Sales"));
                $title->prepend(__("Orders"));
                $title->prepend(sprintf("#%s", $order->getIncrementId()));
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(__('Exception occurred during order load'));
                $resultRedirect->setPath('sales/order/index');
                return $resultRedirect;
            }
            return $this->_view->getPage();
        }
        $resultRedirect->setPath('sales/*/');
        return $resultRedirect;
    }
}
