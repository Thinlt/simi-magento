<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order\Creditmemo\View;

/**
 * Invoice view form
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\View\Form
{
    /**
     * Get vendor order
     * @return /Vnecoms/VendorsSales/Model/Order
     */
    public function getVendorOrder()
    {
        return $this->_coreRegistry->registry('vendor_order');
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
