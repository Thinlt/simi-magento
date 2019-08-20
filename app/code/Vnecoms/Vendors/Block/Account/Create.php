<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Account;

use Magento\Framework\App\ObjectManager;

class Create extends \Magento\Customer\Block\Form\Register
{
    /**
     * Get post action URL
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->isLoggedIn()?$this->getUrl('*/*/RegisterPost'):$this->_customerUrl->getRegisterPostUrl();
    }
    
    /**
     * Get back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account');
    }
    
    /**
     * Get validation URL
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate');
    }
    
    /**
     * Get Success URL
     *
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->getUrl('customer/account');
    }
    
    /**
     * Get Success URL
     *
     * @return string
     */
    public function getErrorUrl()
    {
        return $this->getUrl('*/*/register');
    }
    
    /**
     * Is Logged In customer
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }
    
    /**
     * Get static block id
     *
     * @return int
     */
    public function getStaticBlockId()
    {
        $om = ObjectManager::getInstance();
        $helper = $om->get('Vnecoms\Vendors\Helper\Data');
        return $helper->getSellerRegisterStaticBlock();
    }
    
    /**
     * Is Enabled Agreement
     *
     * @return int
     */
    public function isEnableAgreement()
    {
        $om = ObjectManager::getInstance();
        $helper = $om->get('Vnecoms\Vendors\Helper\Data');
        return $helper->isEnabledRegistrationAgreement();
    }
    
    /**
     * Get Agreement Label
     *
     * @return string;
     */
    public function getAgreementLabel()
    {
        $om = ObjectManager::getInstance();
        $helper = $om->get('Vnecoms\Vendors\Helper\Data');
        return $helper->getAgreementLabel();
    }
}
