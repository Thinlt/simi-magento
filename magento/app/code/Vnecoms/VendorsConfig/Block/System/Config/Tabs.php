<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * System configuration tabs block
 *
 * @method setTitle(string $title)
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Vnecoms\VendorsConfig\Block\System\Config;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Tabs extends \Magento\Config\Block\System\Config\Tabs
{
    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Vnecoms_VendorsConfig::system/config/tabs.phtml';
}
