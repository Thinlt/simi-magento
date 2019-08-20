<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Vendors\Page;

/**
 * Vendor Title Block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Title extends \Vnecoms\Vendors\Block\Vendors\AbstractBlock
{
    /**
     * Get short title
     * @return string
     */
    public function getTitle()
    {
        return $this->pageConfig->getTitle()->getShort();
    }
}
