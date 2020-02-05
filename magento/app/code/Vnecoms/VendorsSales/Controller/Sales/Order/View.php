<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Controller\Sales\Order;

class View extends \Vnecoms\VendorsSales\Controller\Sales\Order
{
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
                $this->_view->loadLayout();
                if ($breadcrumbsBlock = $this->_view->getLayout()->getBlock('breadcrumbs')) {
                    $breadcrumbsBlock->addCrumb(
                        'order_view',
                        [
                            'label' => '#'.$order->getIncrementId(),
                            'title' => '#'.$order->getIncrementId(),
                        ]
                    );
                }
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
        $resultRedirect->setPath('marketplace/*/');
        return $resultRedirect;
    }

}
