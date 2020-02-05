<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order\View;

/**
 * Order view tabs
 */
class Info extends \Magento\Sales\Block\Adminhtml\Order\View\Info
{
    /**
     * Get vendor order
     * @return \Vnecoms\VendorsSales\Model\Order
     */
    public function getVendorOrder()
    {
        if ($vendorOrder = $this->_coreRegistry->registry('vendor_order')) {
            return $vendorOrder;
        } elseif ($vendorInvoice = $this->_coreRegistry->registry('vendor_invoice')) {
            return $vendorInvoice->getOrder();
        }
    }
    
    /**
     * Get order status label
     * @return string
     */
    public function getOrderStatusLabel()
    {
        return $this->getOrder()->getConfig()->getStatusLabel($this->getVendorOrder()->getStatus());
    }
    
    /**
     * Get order view URL.
     *
     * @param int $orderId
     * @return string
     */
    public function getViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $this->getVendorOrder()->getId()]);
    }
}
