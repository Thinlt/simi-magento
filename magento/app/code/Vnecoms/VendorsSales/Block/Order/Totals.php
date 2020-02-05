<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Order;

use Vnecoms\VendorsSales\Model\Order;

class Totals extends \Magento\Sales\Block\Order\Totals
{
    /**
     * @var Order|null
     */
    protected $_vendorOrder = null;

    /**
     * Get order object
     *
     * @return Order
     */
    public function getVendorOrder()
    {
        if ($this->_vendorOrder === null) {
            if ($this->hasData('vendor_order')) {
                $this->_vendorOrder = $this->_getData('vendor_order');
            } elseif ($this->_coreRegistry->registry('current_vendor_order')) {
                $this->_vendorOrder = $this->_coreRegistry->registry('current_vendor_order');
            } elseif ($this->getParentBlock()->getVendorOrder()) {
                $this->_vendorOrder = $this->getParentBlock()->getVendorOrder();
            }
        }

        return $this->_vendorOrder;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function setVendorOrder($order)
    {
        $this->_vendorOrder = $order;
        return $this;
    }

    /**
     * Get totals source object
     *
     * @return Order
     */
    public function getSource()
    {
        return $this->getVendorOrder();
    }
}
