<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\VendorMapping\Model\Api;

use Simi\VendorMapping\Api\VendorLogoutInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\App\ObjectManager;
use Vnecoms\Vendors\Model\Session;


// class VendorLogout extends \Magento\Framework\App\Action\Action implements VendorLogoutInterface
class VendorLogout implements VendorLogoutInterface
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $session;
    
    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;
    
    /**
     * @var PhpCookieManager
     */
    protected $cookieMetadataManager;
    
    /**
     * @param Context $context
     * @param \Vnecoms\Vendors\Model\Session $session
     */
    // public function __construct(
    //     Context $context,
    //     \Vnecoms\Vendors\Model\Session $session
    // ) {
    //     $this->session = $session;
    // }
    
    protected function getSession(){
        if (!$this->session) {
            $this->session = ObjectManager::getInstance()->get(Session::class);
        }
        return $this->session;
    }

    /**
     * Retrieve cookie manager
     *
     * @deprecated 100.1.0
     * @return PhpCookieManager
     */
    protected function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(PhpCookieManager::class);
        }
        return $this->cookieMetadataManager;
    }
    
    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated 100.1.0
     * @return CookieMetadataFactory
     */
    protected function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        }
        return $this->cookieMetadataFactory;
    }

    public function logoutPost(){
        $lastCustomerId = $this->getSession()->getId();
        $this->getSession()->logout()//->setBeforeAuthUrl($this->_redirect->getRefererUrl())
            ->setLastCustomerId($lastCustomerId);
        $this->getSession()->regenerateId();// fix bug logout sessionId = 'deleted'
        if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
            $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
            $metadata->setPath('/');
            $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
        }
        return [[
            'status' => '1',
            'message' => __('You have been logged out successfully.')
        ]];
    }

    /**
     * @return void
     */
    public function execute()
    {
        return null;
    }
}
