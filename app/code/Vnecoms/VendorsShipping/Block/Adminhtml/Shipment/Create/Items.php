<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Block\Adminhtml\Shipment\Create;

/**
 * Adminhtml shipment items grid
 */
class Items extends \Vnecoms\VendorsSales\Block\Adminhtml\Shipment\Create\Items
{
    /**
     * Checks the possibility of creating shipping label by current carrier
     *
     * @return bool
     */
    public function canCreateShippingLabel()
    {
        $shippingCarrier = $this->_carrierFactory->create(
            $this->getVendorOrder()->getShippingMethod(true)->getCarrierCode()
        );
        return $shippingCarrier && $shippingCarrier->isShippingLabelsAvailable();
    }
}
