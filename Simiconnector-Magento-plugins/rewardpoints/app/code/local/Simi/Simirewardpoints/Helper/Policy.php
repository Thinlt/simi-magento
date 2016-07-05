<?php
/**
 * Simi
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Simi.com license that is
 * available through the world-wide-web at this URL:
 * http://www.simi.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @copyright   Copyright (c) 2012 Simi (http://www.simi.com/)
 * @license     http://www.simi.com/license-agreement.html
 */

/**
 * Simirewardpoints Policy Helper
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Helper_Policy extends Mage_Core_Helper_Abstract
{
    const XML_PATH_SHOW_POLICY  = 'simirewardpoints/general/show_policy_menu';
    const XML_PATH_POLICY_PAGE  = 'simirewardpoints/general/policy_page';
    const XML_PATH_SHOW_WELCOME  = 'simirewardpoints/general/show_welcome_page';
    
    /**
     * get Policy URL, return the url to view Policy
     * 
     * @return string
     */
    public function getPolicyUrl()
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_SHOW_POLICY)) {
            return Mage::getUrl('simirewardpoints/index/index');
        }
        return Mage::getUrl('simirewardpoints/index/policy');
    }
    public function getWelcomeUrl()
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_SHOW_WELCOME)) {
            return Mage::getUrl('simirewardpoints/index/index');
        }
        return Mage::getUrl(null, array('_direct' => Mage::getStoreConfig('simirewardpoints/general/welcome_page')));
    }
    
    /**
     * Check policy menu configuration
     * 
     * @param mixed $store
     * @return boolean
     */
    public function showPolicyMenu($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_POLICY, $store);
    }
}
