<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Loyalty
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Loyalty Model
 * 
 * @category    
 * @package     Loyalty
 * @author      Developer
 */
class Simi_Simirewardpoints_Model_Simiappmapping extends Mage_Core_Model_Abstract {

    public function getRewardInfo() {
        $list = array();
        // Collect Info - Customer Points (if logged in)
        $session = Mage::getSingleton('customer/session');
        // $customer = $session->getCustomer();
        $groupId = $session->isLoggedIn() ? $session->getCustomerGroupId() : Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID);
        $helper = Mage::helper('simirewardpoints/point');

        if ($session->isLoggedIn()) {
            $list['loyalty_point'] = (int) Mage::helper('simirewardpoints/customer')->getBalance();
            $list['loyalty_balance'] = Mage::helper('simirewardpoints/customer')->getBalanceFormated();
            $list['loyalty_redeem'] = $this->getMenuBalance();
            $holdingBalance = Mage::helper('simirewardpoints/customer')->getAccount()->getHoldingBalance();
            if ($holdingBalance > 0) {
                $list['loyalty_hold'] = $helper->format($holdingBalance);
            }
            $list['loyalty_image'] = $helper->getImage();
            // Notification Settings
            $list['is_notification'] = (int) Mage::helper('simirewardpoints/customer')->getAccount()->getData('is_notification');
            $list['expire_notification'] = (int) Mage::helper('simirewardpoints/customer')->getAccount()->getData('expire_notification');
        }

        // Earning Point policy
        $earningRate = Mage::getModel('simirewardpoints/rate')->getRate(Simi_Simirewardpoints_Model_Rate::MONEY_TO_POINT, $groupId);
        if ($earningRate && $earningRate->getId()) {
            $spendingMoney = Mage::app()->getStore()->convertPrice($earningRate->getMoney(), true, false);
            $earningPoints = $helper->format($earningRate->getPoints());
            $list['earning_label'] = $helper->__('How you can earn points');
            $list['earning_policy'] = $helper->__('Each %s spent for your order will earn %s.', $spendingMoney, $earningPoints);
        }

        // Spending Point policy
        $block = Mage::getBlockSingleton('simirewardpoints/account_dashboard_policy');
        $redeemablePoints = $block->getRedeemablePoints();
        $spendingRate = Mage::getModel('simirewardpoints/rate')->getRate(Simi_Simirewardpoints_Model_Rate::POINT_TO_MONEY, $groupId);
        if ($spendingRate && $spendingRate->getId()) {
            $spendingPoint = $helper->format($spendingRate->getPoints());
            $getDiscount = Mage::app()->getStore()->convertPrice($spendingRate->getMoney(), true, false);
            $list['spending_label'] = $helper->__('How you can spend points');
            $list['spending_policy'] = $helper->__('Each %s can be redeemed for %s.', $spendingPoint, $getDiscount);
            $list['spending_point'] = (int) $spendingRate->getPoints();
            $list['spending_discount'] = $getDiscount;
            $redeemablePoints = max($redeemablePoints, $spendingRate->getPoints());
            $baseAmount = $redeemablePoints * $spendingRate->getMoney() / $spendingRate->getPoints();
            $list['start_discount'] = Mage::app()->getStore()->convertPrice($baseAmount, true, false);
        }
        $list['spending_min'] = (int) $redeemablePoints;
        if ($redeemablePoints > (int) Mage::helper('simirewardpoints/customer')->getBalance()) {
            $invertPoint = $redeemablePoints - Mage::helper('simirewardpoints/customer')->getBalance();
            $list['invert_point'] = $helper->format($invertPoint);
        }

        // Other Policy Infomation
        $policies = array();
        if ($_expireDays = $block->getTransactionExpireDays()) {
            $policies[] = $helper->__('A transaction will expire after %s since its creating date.', $_expireDays . ' ' . ($_expireDays == 1 ? $helper->__('day') : $helper->__('days'))
            );
        }
        if ($_holdingDays = $block->getHoldingDays()) {
            $policies[] = $helper->__('A transaction will be withheld for %s since creation.', $_holdingDays . ' ' . ($_holdingDays == 1 ? $helper->__('day') : $helper->__('days'))
            );
        }
        if ($_maxBalance = $block->getMaxPointBalance()) {
            $policies[] = $helper->__('Maximum of your balance') . ': ' . $helper->format($_maxBalance) . '.';
        }
        if ($_redeemablePoints = $block->getRedeemablePoints()) {
            $policies[] = $helper->__('Reach %s to start using your balance for your purchase.', $helper->format($_redeemablePoints)
            );
        }
        if ($_maxPerOrder = $block->getMaxPerOrder()) {
            $policies[] = $helper->__('Maximum %s are allowed to spend for an order.', $helper->format($_maxPerOrder)
            );
        }
        $list['policies'] = $policies;
        return $list;
    }

    public function getHistory() {
        $session = Mage::getSingleton('customer/session');
        $collection = Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('customer_id', $session->getCustomerId());
        $collection->getSelect()->order('created_time DESC');
        return $collection;
    }

    public function spendPoints($data) {
        $list = array();
        Mage::app()->getRequest()->setControllerModule('Simi_Connector');
        // Checkout session: spend points
        $session = Mage::getSingleton('checkout/session');
        if ($data->usepoint) {
            $session->setData('use_point', true);
            $session->setRewardSalesRules(array(
                'rule_id' => $data->ruleid,
                'use_point' => $data->usepoint,
            ));
        } else {
            $session->unsetData('use_point');
        }
        // Return Total Information
        $quote = $session->getQuote();
        $quote->collectTotals()->save();

        // Total checkout
        $total = $quote->getTotals();
        $grandTotal = $total['grand_total']->getValue();
        $subTotal = $total['subtotal']->getValue();
        $discount = 0;
        if (isset($total['discount']) && $total['discount']) {
            $discount = abs($total['discount']->getValue());
        }
        if (isset($total['tax']) && $total['tax']->getValue()) {
            $tax = $total['tax']->getValue();
        } else {
            $tax = 0;
        }
        if ($quote->getCouponCode()) {
            $coupon = $quote->getCouponCode();
        } else {
            $coupon = '';
        }
        $total_data = array(
            'sub_total' => $subTotal,
            'grand_total' => $grandTotal,
            'discount' => $discount,
            'tax' => $tax,
            'coupon_code' => $coupon,
        );
        $fee_v2 = array();
        Mage::helper('connector/checkout')->setTotal($total, $fee_v2);
        $total_data['v2'] = $fee_v2;
        $list['fee'] = $this->changeData($total_data, 'connector_checkout_get_order_config_total', array('object' => $this));

        // Payment
        $totalPay = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
        $payment = Mage::getModel('connector/checkout_payment');
        Mage::dispatchEvent('simi_add_payment_method', array('object' => $payment));
        $paymentMethods = $payment->getMethods($quote, $totalPay);
        $list_payment = array();
        foreach ($paymentMethods as $method) {
            $list_payment[] = $payment->getDetailsPayment($method);
        }
        $list['payment_method_list'] = $this->changeData($list_payment, 'simicart_change_payment_detail', array('object' => $this));

        Mage::app()->getRequest()->setControllerModule('Magestore_Loyalty');
        // Return Data formatted
        $information = $this->statusSuccess();
        $information['data'] = array($list);
        return $information;
    }

    public function saveSettings($data) {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $rewardAccount = Mage::getModel('simirewardpoints/customer')->load($customerId, 'customer_id');
            if (!$rewardAccount->getId()) {
                $rewardAccount->setCustomerId($customerId)
                        ->setData('point_balance', 0)
                        ->setData('holding_balance', 0)
                        ->setData('spent_balance', 0);
            }
            $rewardAccount->setIsNotification((boolean) $data->is_notification)
                    ->setExpireNotification((boolean) $data->expire_notification);
            try {
                $rewardAccount->save();
            } catch (Exception $e) {
                return $this->statusError(array($e->getMessage()));
            }
        } else {
            return $this->statusError(array(Mage::helper('loyalty')->__('Your session has been expired. Please relogin and try again.')));
        }
        // Return Data formatted
        $information = $this->statusSuccess();
        $information['data'] = array('success' => 1);
        return $information;
    }

    public function getMenuBalance() {
        $helper = Mage::helper('simirewardpoints/customer');
        $pointAmount = $helper->getBalance();
        if ($pointAmount > 0) {
            $rate = Mage::getModel('simirewardpoints/rate')->getRate(Simi_Simirewardpoints_Model_Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                return Mage::app()->getStore()->convertPrice($baseAmount, true, false);
            }
        }
        return $helper->getBalanceFormated();
    }

}
