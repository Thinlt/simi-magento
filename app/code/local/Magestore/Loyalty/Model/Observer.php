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
 * Loyalty Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @author      Magestore Developer
 */
class Magestore_Loyalty_Model_Observer
{
    public function salesQuoteCollectTotalsAfter($observer)
    {
    	if (Mage::app()->getRequest()->getControllerModule() == 'Simi_Connector' || Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('rewardpoints/total_quote_earning')
                ->salesQuoteCollectTotalsAfter($observer);
    	}
    }
    
    /**
     * Attach Reward Information when load product detail
     */
    public function productDetailRewardPoints($observer)
    {
    	// if (!Mage::helper('loyalty')->isShowOnProduct()) {
    	// 	return ;
    	// }
    	// $model     = $observer['object'];
    	// $product   = $observer['product'];
    	
    	// $block = Mage::getBlockSingleton('rewardpoints/product_view_earning');
    	// if (!Mage::registry('product')) {
    	// 	Mage::register('product', $product);
    	// }
    	// if ($block->hasEarningRate()) {
     //        $_product  = $model->getData();
            
     //        $_product['loyalty_image'] = Mage::helper('rewardpoints/point')->getImage();
     //        $_product['loyalty_label'] = $block->__('You could receive some %s for purchasing this product', $block->getPluralPointName());
            
     //        $model->setData($_product);
    	// }


        $show_info = Mage::getStoreConfig('rewardpoints/product_page/show_information', Mage::app()->getStore()->getId());
        $show_list_info = Mage::getStoreConfig('rewardpoints/product_page/show_list_points', Mage::app()->getStore()->getId());
        $arr_product_types = array("Mage_Catalog", "Mage_Bundle", "OrganicInternet_SimpleConfigurableProducts_Catalog");
        if ($show_info){
            $model     = $observer['object'];
            $product   = $observer['product'];
            $_product  = $model->getData();
            $_loyalty = Mage::helper('loyalty/data')->getPointsOnProductPages($product);
            $_product['loyalty_image'] = $_loyalty['image'];
            $_product['loyalty_label'] = $_loyalty['content'];
            $_product['loyalty_more'] = $_loyalty['extra_point'];
            $model->setData($_product);
        }
    }
    
    /**
     * Get Reward Points Configuration for Customer Checkout
     */
    public function checkoutOrderConfigRewardPoints($observer)
    {
    	$helper = Mage::helper('loyalty/block_spend');
    	$model = $observer['object'];
    	$pointSpending = 0;
    	$pointDiscount = 0.00;
    	$pointEarning  = 0;
        $quote = Mage::getSingleton('checkout/session')->getQuote();
    	foreach ($quote->getAllAddresses() as $address) {
    		if ($address->getDiscountAmount()) {
    			$pointSpending = (int)Mage::helper('rewardpoints/event')->getCreditPoints($address->getQuote());
                $pointDiscount = 0;
    			// $pointDiscount = $address->getDiscountAmount();
    		}
    		if ($address->getRewardpointsEarn()) {
    			$pointEarning = (int)$address->getRewardpointsEarn();
    		}
    	}
    	$model->setLoyaltySpend($pointSpending);
    	$model->setLoyaltyDiscount($pointDiscount);
    	$model->setLoyaltyEarn($pointEarning);
    	$model->setLoyaltySpending(Mage::helper('rewardpoints')->__('%s brio coins used', $pointSpending));
    	$model->setLoyaltyEarning(Mage::helper('rewardpoints')->__('%s brio coins earning', $pointEarning));
    	$model->setLoyaltyRules($helper->getSliderRulesFormatted());
    }
    
    /**
     * Update menu after checkout
     */
    public function checkoutOrderPlaceAfter($observer)
    {
    	if (Mage::getSingleton('customer/session')->isLoggedIn()) {
    	   $model = $observer['object'];
    	   $model->setLoyaltyBalance(Mage::helper('loyalty')->getMenuBalance());
    	}
    }
}
