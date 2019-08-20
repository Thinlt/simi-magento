<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sales Order Email order items
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Vnecoms\VendorsSales\Block\Order\Email;

class Items extends \Magento\Sales\Block\Items\AbstractItems
{

    /**
     * Prepare item before output
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $renderer
     * @return void
     */
    protected function _prepareItem(\Magento\Framework\View\Element\AbstractBlock $renderer)
    {
        $vendorOrder = $this->getVendorOrder();
        $renderer->getItem()->setVendorOrder($vendorOrder);
    }
}
