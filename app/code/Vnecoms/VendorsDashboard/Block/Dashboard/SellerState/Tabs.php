<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Vendor tabs
 */
namespace Vnecoms\VendorsDashboard\Block\Dashboard\SellerState;

class Tabs extends \Vnecoms\VendorsDashboard\Block\Vendors\Dashboard\SellerState\Tabs
{
    /**
     * Get Last Transaction URL
     *
     * @return string
     */
    public function getLastTransactionUrl()
    {
        return $this->getUrl('marketplace/dashboard_grid/lastTransaction', ['_current' => true]);
    }
    
    /**
     * Get Bestseller URL
     *
     * @return string
     */
    public function getBestsellerUrl()
    {
        return $this->getUrl('marketplace/dashboard_grid/bestseller', ['_current' => true]);
    }
    
    /**
     * Get Most Viewed URL
     *
     * @return string
     */
    public function getMostViewedUrl()
    {
        return $this->getUrl('marketplace/dashboard_grid/mostViewed', ['_current' => true]);
    }
    
    /**
     * Get order tabs content
     *
     * @return string
     */
    public function getOrderTabContent()
    {
        return $this->getLayout()->createBlock(
            'Vnecoms\VendorsDashboard\Block\Dashboard\Order\Grid'
        )->toHtml();
    }
}
