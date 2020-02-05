<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Vendors;

/**
 * Adminhtml footer block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Error404 extends \Magento\Framework\View\Element\Template
{
    /**
     * Get dashboard URL
     * @return string
     */
    public function getDashboardUrl()
    {
        return $this->getUrl('dashboard');
    }
}
