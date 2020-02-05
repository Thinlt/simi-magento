<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Block\Adminhtml\Shipment\Create;

/**
 * Adminhtml shipment create form
 */
class Form extends \Vnecoms\VendorsSales\Block\Adminhtml\Shipment\Create\Form
{
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
