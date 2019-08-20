<?php
/**
 * @category    Magento
 * @package     Magento_Sales
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order;

/**
 * Adminhtml sales order view
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Order\Totals
{
    /**
     * Retrieve vendor order
     *
     * @return mixed|\Vnecoms\VendorsSales\Model\Order
     */
    public function getVendorOrder()
    {
        return $this->_coreRegistry->registry('vendor_order');
    }
}
