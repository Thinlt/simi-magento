<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order\View;

use Vnecoms\VendorsSales\Model\Order;

/**
 * Order view tabs
 */
class Shipping extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{

    /**
     * Get vendor order.
     * @return \Vnecoms\VendorsSales\Model\Order
     */
    public function getVendorOrder()
    {
        return $this->_coreRegistry->registry('vendor_order');
    }


    /**
     * can view shipping tracking , create lable shipment online
     * @return bool
     */
    public function canViewShippingInfo()
    {
        if (is_object($this->getVendorOrder()->getShippingMethod())
            && !$this->getVendorOrder()->getShippingMethod()->getMethod()) {
            return false;
        } elseif (!$this->getVendorOrder()->getShippingMethod()) {
            return false;
        }
        return true;
    }

    /**
     * Get price data object
     *
     * @return Order|mixed
     */
    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if ($obj === null) {
            return $this->getVendorOrder();
        }
        return $obj;
    }
}
