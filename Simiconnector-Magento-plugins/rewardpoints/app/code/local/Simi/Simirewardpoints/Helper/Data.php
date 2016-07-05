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
 * Simirewardpoints Helper
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLE = 'simirewardpoints/general/enable';
    
    /**
     * check reward points system is enabled
     * 
     * @param mixed $store
     * @return boolean
     */
    public function isEnable($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE, $store);
    }
    public function isEnableOutput(){
        return Mage::helper('core')->isModuleOutputEnabled('Simi_Simirewardpoints');
    }
    public function isEnablePolicy($store = null){
        return Mage::getStoreConfig('simirewardpoints/general/show_policy_menu', $store);
    }
    public function getPolicyLink($store = null){
        if(!$this->isEnablePolicy()) return null;
        return Mage::getUrl('simirewardpoints/index/policy');
    }
    
    /**
     * get rewards points label to show on Account Navigation
     * 
     * @return string
     */
    public function getMyRewardsLabel()
    {
        $pointAmount = Mage::helper('simirewardpoints/customer')->getBalance();
        if ($pointAmount > 0) {
            $rate = Mage::getModel('simirewardpoints/rate')->getRate(Simi_Simirewardpoints_Model_Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                $pointAmount = Mage::app()->getStore()->convertPrice($baseAmount, true);
            }
        }
        $imageHtml = Mage::helper('simirewardpoints/point')->getImageHtml(false);
        return $this->__('My SimiCart Rewards') . ' ' . $pointAmount . $imageHtml;
    }
    
}
