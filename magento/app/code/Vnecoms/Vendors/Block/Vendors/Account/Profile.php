<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Vendors\Account;

use Magento\Framework\App\ObjectManager;

/**
 * Base widget class
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Profile extends \Vnecoms\Vendors\Block\Profile
{
    /**
     * Get vendor object
     *
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor()
    {
        $om = ObjectManager::getInstance();
        $session = $om->get('Vnecoms\Vendors\Model\Session');
        return $session->getVendor();
    }
}
