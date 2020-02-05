<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order\Invoice\View;

/**
 * Invoice view form
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\Invoice\View\Form
{
    /**
     * Get vendor invoice
     * @return \Vnecoms\VendorsSales\Model\Order\Invoice
     */
    public function getVendorInvoice()
    {
        return $this->_coreRegistry->registry('vendor_invoice');
    }
    
    /**
     * Get vendor order.
     * @return \Vnecoms\VendorsSales\Model\Order
     */
    public function getVendorOrder()
    {
        return $this->getVendorInvoice()->getOrder();
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
