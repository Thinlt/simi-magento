<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Account\Login;

/**
 * Customer login info block
 */
class Info extends \Magento\Framework\View\Element\Template
{

    /**
     * Retrieve create new account url
     *
     * @return string
     */
    public function getCreateAccountUrl()
    {
        return $this->getUrl('marketplace/seller/register');
    }
    
    public function isAllowedRegister()
    {
        return true;
    }
}
