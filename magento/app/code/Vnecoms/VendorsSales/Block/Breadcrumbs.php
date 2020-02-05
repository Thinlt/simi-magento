<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Vendor tabs
 */
namespace Vnecoms\VendorsSales\Block;

class Breadcrumbs extends \Magento\Framework\View\Element\Template
{
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
/*             $breadcrumbsBlock->addCrumb(
                'dashboard',
                [
                    'label' => __('Home'),
                    'title' => __('Seller Dashboard'),
                    'link' => $this->getUrl('marketplace/dashboard')
                ]
            ); */
        }
    }
}
