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
 * Simirewardpoints Rewrite to fix error with Paygate Authorizenet
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Model_Paygate_Rewrite_Authorizenet extends Mage_Paygate_Model_Authorizenet
{
    /**
     * Send request with new payment to gateway
     *
     * @param Mage_Payment_Model_Info $payment
     * @param decimal $amount
     * @param string $requestType
     * @return Mage_Paygate_Model_Authorizenet
     * @throws Mage_Core_Exception
     */
    protected function _place($payment, $amount, $requestType)
    {
        /** @var $helper Simi_Simirewardpoints_Helper_Calculation_Spending */
        $helper = Mage::helper('simirewardpoints/calculation_spending');
        
        $rewardPointsDiscount  = $helper->getPointItemDiscount();
        $rewardPointsDiscount += $helper->getCheckedRuleDiscount();
        $rewardPointsDiscount += $helper->getSliderRuleDiscount();
        
        $container = new Varien_Object(array(
            'reward_points_discount' => $rewardPointsDiscount
        ));
        Mage::dispatchEvent('simirewardpoints_rewrite_authorizenet_place', array(
            'container' => $container
        ));
        
        if ($container->getSimirewardpointsDiscount() > 0 && $requestType == self::REQUEST_TYPE_AUTH_ONLY) {
            $amount -= Mage::app()->getStore()->convertPrice($container->getSimirewardpointsDiscount());
        }
        
        return parent::_place($payment, $amount, $requestType);
    }
}
