<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Loyalty Helper
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @author      Magestore Developer
 */
class Magestore_Loyalty_Helper_Block_Spend extends Mage_Core_Helper_Abstract
{
    /**
     * get slider rule formatted
     * 
     * @param array $rules
     * @return string
     */
    public function getSliderRulesFormatted($rules = null)
    {
        $result = array();        
        $rewardBlock = Mage::getBlockSingleton('rewardpoints/coupon');
        // Zend_debug::dump(get_class($rewardBlock));die();
        $ruleOptions = array('id' => '0');
        $ruleOptions['minPoints'] = 0;
        $ruleOptions['pointStep'] =  Mage::getStoreConfig('rewardpoints/default/step_value', Mage::app()->getStore()->getId());
        $maxPoints = $rewardBlock->getCustomerPoints();        
        $ruleOptions['maxPoints'] = max(0, $maxPoints);
        $ruleOptions['pointStepLabel'] = Mage::helper('rewardpoints')->__('%s points', $ruleOptions['pointStep']);
        $ruleOptions['pointStepDiscount'] = Mage::helper('core')->formatPrice(1, false);
        $ruleOptions['optionType'] = 'slider';        
        $result[] = $ruleOptions;        
        return $result;
    }
        
}
