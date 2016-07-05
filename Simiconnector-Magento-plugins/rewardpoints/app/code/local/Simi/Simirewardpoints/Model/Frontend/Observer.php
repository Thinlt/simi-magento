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
 * Simirewardpoints Frontend Observer Model
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Model_Frontend_Observer {

    /**
     * add speding points block into payment method
     * 
     * @param type $observer
     */
    public function simirewardpointsPaymentMethod($observer) {
        $block = $observer['block'];
        if (($block instanceof Mage_Checkout_Block_Onepage_Payment_Methods ||  $block instanceof Simi_Webpos_Block_Onepage_Payment_Methods)  && Mage::helper('simirewardpoints')->isEnableOutput()) {
            $requestPath = $block->getRequest()->getRequestedRouteName()
                    . '_' . $block->getRequest()->getRequestedControllerName()
                    . '_' . $block->getRequest()->getRequestedActionName();
            if ($requestPath == 'checkout_onepage_index' &&  ! Mage::helper('core')->isModuleOutputEnabled('Amasty_Scheckout')) {
                return;
            }
//            hiepdd use max points default
            $checkoutSession = Mage::getSingleton('checkout/session');
            $spendingHelper = Mage::helper('simirewardpoints/calculation_spending');
            if ($checkoutSession->getData('use_max') !==0 && $spendingHelper->isUseMaxPointsDefault()) {
                $checkoutSession->setData('use_point', 1);
                $checkoutSession->setData('use_max', 1);
            } 
//            hiepdd end
            $transport = $observer['transport'];
            $html_addsimirewardpoints = $block->getLayout()->createBlock('simirewardpoints/checkout_onepage_payment_rewrite_methods')->renderView();
            $html = $transport->getHtml();
//            if (version_compare(Mage::getVersion(), '1.8.0', '>=') && Mage::app()->getRequest()->getRouteName() == 'checkout') {
//                $html = '<dl class="sp-methods" id="checkout-payment-method-load">' . $html . '</dl>';
//            }
            $html .= '<script type="text/javascript">checkOutLoadSimirewardpoints(' . Mage::helper('core')->jsonEncode(array('html' => $html_addsimirewardpoints)) . ');</script>';

            $transport->setHtml($html);
        }
    }

    /**
     * transfer reward points discount to Paypal gateway
     * 
     * @param type $observer
     */
    public function paypalPrepareLineItems($observer) {
        if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
            if ($paypalCart = $observer->getPaypalCart()) {
                $salesEntity = $paypalCart->getSalesEntity();

                $baseDiscount = $salesEntity->getSimirewardpointsBaseDiscount();
                if ($baseDiscount < 0.0001 && $salesEntity instanceof Mage_Sales_Model_Quote) {
                    $helper = Mage::helper('simirewardpoints/calculation_spending');
                    $baseDiscount = $helper->getPointItemDiscount();
                    $baseDiscount += $helper->getCheckedRuleDiscount();
                    $baseDiscount += $helper->getSliderRuleDiscount();
                }
                //$baseDiscount -= $salesEntity->getSimirewardpointsBaseHiddenTaxAmount();
                if ($baseDiscount > 0.0001) {
                    $paypalCart->updateTotal(
                            Mage_Paypal_Model_Cart::TOTAL_DISCOUNT, (float) $baseDiscount, Mage::helper('simirewardpoints')->__('Use points on spend')
                    );
                }
            }
            return $this;
        }
        $salesEntity = $observer->getSalesEntity();
        $additional = $observer->getAdditional();
        if ($salesEntity && $additional) {
            $baseDiscount = $salesEntity->getSimirewardpointsBaseDiscount();
            if ($baseDiscount < 0.0001 && $salesEntity instanceof Mage_Sales_Model_Quote) {
                $helper = Mage::helper('simirewardpoints/calculation_spending');
                $baseDiscount = $helper->getPointItemDiscount();
                $baseDiscount += $helper->getCheckedRuleDiscount();
                $baseDiscount += $helper->getSliderRuleDiscount();
            }

            if ($baseDiscount > 0.0001) {
                $items = $additional->getItems();
                $items[] = new Varien_Object(array(
                    'name' => Mage::helper('simirewardpoints')->__('Use points on spend'),
                    'qty' => 1,
                    'amount' => -(float) $baseDiscount,
                ));
                $additional->setItems($items);
            }
        }
    }

}
