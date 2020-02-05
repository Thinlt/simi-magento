<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsDashboard\Block\Dashboard\Order;

/**
 * Adminhtml seller dashboard recent transaction grid
 *
 */
class Grid extends \Vnecoms\VendorsDashboard\Block\Vendors\Dashboard\Order\Grid
{
    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('marketplace/sales_order/view', ['order_id' => $row->getId()]);
    }
}
