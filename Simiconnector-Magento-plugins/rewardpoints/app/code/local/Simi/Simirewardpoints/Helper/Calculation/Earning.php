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
 * Simirewardpoints Earning Calculation Helper
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Helper_Calculation_Earning extends Simi_Simirewardpoints_Helper_Calculation_Abstract {

    const XML_PATH_EARNING_EXPIRE = 'simirewardpoints/earning/expire';
    const XML_PATH_EARNING_ORDER_INVOICE = 'simirewardpoints/earning/order_invoice';
    const XML_PATH_HOLDING_DAYS = 'simirewardpoints/earning/holding_days';
    const XML_PATH_ORDER_CANCEL_STATUS = 'simirewardpoints/earning/order_cancel_state';
    const XML_PATH_EARNING_BY_SHIPPING = 'simirewardpoints/earning/by_shipping';
    const XML_PATH_EARNING_BY_TAX = 'simirewardpoints/earning/by_tax';

    /**
     * get Total Point that customer can earn by purchase current order/ quote
     * 
     * @param null|Mage_Sales_Model_Quote $quote
     * @return int
     */
    public function getTotalPointsEarning($quote = null) {
        if (is_null($quote)) {
            $quote = $this->getQuote();
        }
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
        
        return $address->getSimirewardpointsEarn();
        
        //Hai.Tran 21/11
//        $grandTotal = $quote->getBaseGrandTotal();
//        if (!Mage::getStoreConfigFlag(self::XML_PATH_EARNING_BY_SHIPPING, $quote->getStoreId())) {
//            $grandTotal -= $address->getBaseShippingAmount();
//			if (Mage::getStoreConfigFlag(self::XML_PATH_EARNING_BY_TAX, $quote->getStoreId())) {
//                $grandTotal -= $address->getBaseShippingTaxAmount();
//            }
//        }
//        if (!Mage::getStoreConfigFlag(self::XML_PATH_EARNING_BY_TAX, $quote->getStoreId())) {
//            $grandTotal -= $address->getBaseTaxAmount();
//        }
//        $grandTotal = max(0, $grandTotal);
//        //end Hai.Tran 21/11
//        $container = new Varien_Object(array(
//            'total_points' => $this->getRateEarningPoints($grandTotal, $quote->getStoreId()),
//        ));
//        Mage::dispatchEvent('simirewardpoints_calculation_earning_total_points', array(
//            'quote' => $quote,
//            'container' => $container,
//        ));
//        return $container->getTotalPoints();
    }
    
     /**
     * get Total Point earning by discount
     * 
     * @param null|Mage_Sales_Model_Quote $quote
     * @return int
     */
    public function getEarningPointByCoupon($quote = null){
        $needConvert = Mage::getStoreConfig('simirewardpoints/general/convert_point');
        if(!$needConvert) return 0;
        
        if (is_null($quote)) {
            $quote = $this->getQuote();
        }
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
        return $address->getSimirewardpointsPointsByDiscount();
    }
    
    /**
     * get Total Point earning by using coupon code
     * 
     * @param null|Mage_Sales_Model_Quote $quote
     * @return int
     */
    public function getCouponEarnPoints($quote = null){
        $needConvert = Mage::getStoreConfig('simirewardpoints/general/convert_point');
        if(!$needConvert) return 0;
        
        if (is_null($quote)) {
            $quote = $this->getQuote();
        }
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
        return $address->getCouponCode();
    }

    /**
     * calculate quote earning points by system rate
     * 
     * @param float $baseGrandTotal
     * @param mixed $store
     * @return int
     */
    public function getRateEarningPoints($baseGrandTotal, $store = null) {
        $customerGroupId = $this->getCustomerGroupId();
        $websiteId = $this->getWebsiteId();

        $cacheKey = "earning_rate_points:$customerGroupId:$websiteId:$baseGrandTotal";
        if ($this->hasCache($cacheKey)) {
            return $this->getCache($cacheKey);
        }
        $rate = Mage::getSingleton('simirewardpoints/rate')->getRate(
                Simi_Simirewardpoints_Model_Rate::MONEY_TO_POINT, $customerGroupId, $websiteId
        );
        if ($rate && $rate->getId()) {
            /**
             * end update
             */
            if ($baseGrandTotal < 0) {
                $baseGrandTotal = 0;
            }
            $points = Mage::helper('simirewardpoints/calculator')->round(
                    $baseGrandTotal * $rate->getPoints() / $rate->getMoney(), $store
            );
            $this->saveCache($cacheKey, $points);
        } else {
            $this->saveCache($cacheKey, 0);
        }
        return $this->getCache($cacheKey);
    }

    /**
     * get current checkout quote
     * 
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote() {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote')->getQuote();
        }
        return Mage::getSingleton('checkout/session')->getQuote();
    }
    /**
	* get shipping earning point from $order
	* @return int
	*/
    public function getShippingEarningPoints($order){
        if(!$order instanceof Mage_Sales_Model_Order){
            return 0;
        }
        $shippingEarningPoints = $order->getSimirewardpointsEarn();
        foreach ($order->getAllItems() as $item){
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildrenItems() as $child) {
                    $shippingEarningPoints -= $child->getSimirewardpointsEarn();
                }
            } elseif ($item->getProduct()) {
                $shippingEarningPoints -= $item->getSimirewardpointsEarn();
            }
        }
        return $shippingEarningPoints;
    }

}
