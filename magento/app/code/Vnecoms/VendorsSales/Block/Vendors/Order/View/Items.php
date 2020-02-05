<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order\View;

/**
 * Order view tabs
 */
class Items extends \Magento\Sales\Block\Adminhtml\Order\View\Items
{
    /**
     * Get vendor order
     * @return \Vnecoms\VendorsSales\Model\Order
     */
    public function getVendorOrder()
    {
        return $this->_coreRegistry->registry('vendor_order');
    }
    
    /**
     * Retrieve order items collection
     *
     * @return Collection
     */
    public function getItemsCollection()
    {
        $items = [];
        $itemCollection = $this->getOrder()->getItemsCollection();
        $vendorOrderId = $this->getVendorOrder()->getId();
        foreach ($itemCollection as $item) {
            if ($item->getVendorOrderId() == $vendorOrderId) {
                $items[] = $item;
            }
        }
        
        return $items;
    }
}
