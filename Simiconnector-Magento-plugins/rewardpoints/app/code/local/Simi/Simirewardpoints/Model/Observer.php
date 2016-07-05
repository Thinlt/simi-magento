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
 * Simirewardpoints Observer Model
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Model_Observer
{
    /**
     * process before place order event
     * 
     * @param type $observer
     * @return Simi_Simirewardpoints_Model_Observer
     */
    public function salesOrderPlaceBefore($observer)
    {
        $order = $observer['order'];
        $quote = $observer['quote'];
        if ($order->getCustomerIsGuest()) {
            return $this;
        }
        
        $totalPointSpent = Mage::helper('simirewardpoints/calculation_spending')->getTotalPointSpent();
        if (!$totalPointSpent) {
            return $this;
        }
        
        $balance = Mage::helper('simirewardpoints/customer')->getBalance();
        if ($balance < $totalPointSpent) {
            throw new Mage_Core_Exception(Mage::helper('simirewardpoints')->__(
                'Your points balance is not enough to spend for this order'
            ));
        }
        
        $minPoint = (int)Mage::getStoreConfig(
            Simi_Simirewardpoints_Helper_Customer::XML_PATH_REDEEMABLE_POINTS,
            $quote->getStoreId()
        );
        if ($minPoint > $balance) {
            throw new Mage_Core_Exception(Mage::helper('simirewardpoints')->__(
                'Minimum points balance allows to redeem is %s',
                Mage::helper('simirewardpoints/point')->format($minPoint, $quote->getStoreId())
            ));
        }
        
        return $this;
    }
    
    /**
     * process after place order event
     * 
     * @param type $observer
     * @return Simi_Simirewardpoints_Model_Observer
     */
    public function salesOrderPlaceAfter($observer)
    {
        $order = $observer['order'];
        $quote = $observer['quote'];
        if ($order->getCustomerIsGuest()) {
            return $this;
        }
        
        // Process spending points for order
        if ($order->getSimirewardpointsSpent() > 0) {
            Mage::helper('simirewardpoints/action')->addTransaction('spending_order',
                $quote->getCustomer(),
                $order
            );
        }
        
        // Clear reward points checkout session
        $session = Mage::getSingleton('checkout/session');
        $session->setCatalogRules(array());
        $session->setData('use_point', 0);
        $session->setRewardSalesRules(array());
        $session->setRewardCheckedRules(array());
        
        return $this;
    }
    
    /**
     * Process order after save
     * 
     * @param type $observer
     * @return Simi_Simirewardpoints_Model_Observer
     */
    public function salesOrderSaveAfter($observer)
    {
        $order = $observer['order'];
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return $this;
        }
        
        // Add earning point for customer
        if ($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE
            && $order->getSimirewardpointsEarn()
        ) {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            if (!$customer->getId()) {
                return $this;
            }
            Mage::helper('simirewardpoints/action')->addTransaction(
                'earning_invoice', $customer, $order
            );
            return $this;
        }
        
        // Check is refund manual
        $input = Mage::app()->getRequest()->getParam('creditmemo');
        if (isset($input['refund_points']) || isset($input['refund_earned_points'])) {
            return $this;
        }
        
        // Refund point that customer used to spend for this order (when order is canceled)
        $refundStatus = (string)Mage::getStoreConfig(
            Simi_Simirewardpoints_Helper_Calculation_Spending::XML_PATH_ORDER_REFUND_STATUS,
            $order->getStoreId()
        );
        $refundStatus = explode(',', $refundStatus);
        if ($order->getStatus() && in_array($order->getStatus(), $refundStatus)) {
            $maxPoint  = $order->getSimirewardpointsSpent();
            $maxPoint -= (int)Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('action', 'spending_cancel')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
			$maxPoint -= (int)Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('action', 'spending_creditmemo')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            if ($maxPoint > 0) {
                $order->setRefundSpentPoints($maxPoint);
                if (empty($customer)) {
                    $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                }
                if (!$customer->getId()) {
                    return $this;
                }
                Mage::helper('simirewardpoints/action')->addTransaction(
                    'spending_cancel', $customer, $order
                );
            }
        }
        
        // Deduct earning point from customer if order is canceled
        $refundStatus = (string)Mage::getStoreConfig(
            Simi_Simirewardpoints_Helper_Calculation_Earning::XML_PATH_ORDER_CANCEL_STATUS,
            $order->getStoreId()
        );
        $refundStatus = explode(',', $refundStatus);
        if ($order->getStatus() && in_array($order->getStatus(), $refundStatus)) {
            if ($order->getSimirewardpointsEarn() <= 0) {
                return $this;
            }
			/* hiepdd */ 
            $maxEarnedRefund  = (int)Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('action', 'earning_invoice')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            $maxEarnedRefund += (int)Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('action', 'earning_creditmemo')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            $maxEarnedRefund += (int)Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('action', 'earning_cancel')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            if ($maxEarnedRefund <= 0) {
                return $this;
            }
            if ($maxEarnedRefund > $order->getSimirewardpointsEarn()) {
                $maxEarnedRefund = $order->getSimirewardpointsEarn();
            }
            if ($maxEarnedRefund > 0) {
                $order->setRefundEarnedPoints($maxEarnedRefund);
                if (empty($customer)) {
                    $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                }
                if (!$customer->getId()) {
                    return $this;
                }
                Mage::helper('simirewardpoints/action')->addTransaction(
                    'earning_cancel', $customer, $order
                );
            }
        }
        
        return $this;
    }
    
    /**
     * Process invoice after save
     * 
     * @param type $observer
     * @return Simi_Simirewardpoints_Model_Observer
     */
    public function salesOrderInvoiceSaveAfter($observer)
    {
        $invoice = $observer['invoice'];
        $order   = $invoice->getOrder();
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()
            || $invoice->getState() != Mage_Sales_Model_Order_Invoice::STATE_PAID
            || !$order->getSimirewardpointsEarn()
        ) {
            return $this;
        }
        if (!Mage::getStoreConfigFlag(
            Simi_Simirewardpoints_Helper_Calculation_Earning::XML_PATH_EARNING_ORDER_INVOICE,
            $order->getStoreId()
        )) {
            return $this;
        }
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        if (!$customer->getId()) {
            return $this;
        }
        
        Mage::helper('simirewardpoints/action')->addTransaction(
            'earning_invoice', $customer, $invoice
        );
        
        return $this;
    }
    
    /**
     * Refine input (from admin) when create creditmemo
     * 
     * @param type $observer
     * @return Simi_Simirewardpoints_Model_Observer
     */
    public function salesOrderCreditmemoRegisterBefore($observer)
    {
        $request    = $observer['request'];
        if($request->getActionName() == "updateQty") return $this;
        
        $creditmemo = $observer['creditmemo'];
        
        $input      = $request->getParam('creditmemo');
        $order      = $creditmemo->getOrder();
        
        // Refund point to customer (that he used to spend)
        if (isset($input['refund_points']) && $input['refund_points'] > 0) {
            $refundPoints = (int)$input['refund_points'];
            
            $maxPoint  = $order->getSimirewardpointsSpent();
            $maxPoint -= (int)Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('action', 'spending_creditmemo')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            
            $refundPoints = min($refundPoints, $maxPoint);
            $creditmemo->setRefundSpentPoints(max($refundPoints, 0));
        }
        
        // Deduce point from customer (that earned from this order)
        if (isset($input['refund_earned_points']) && $input['refund_earned_points'] > 0) {
            $refundPoints = (int)$input['refund_earned_points'];
            
            $maxEarnedRefund  = (int)Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('action', 'earning_invoice')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            if ($maxEarnedRefund > $order->getSimirewardpointsEarn()) {
                $maxEarnedRefund = $order->getSimirewardpointsEarn();
            }
            $maxEarnedRefund += (int)Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('action', 'earning_creditmemo')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            if ($maxEarnedRefund > $order->getSimirewardpointsEarn()) {
                $maxEarnedRefund = $order->getSimirewardpointsEarn();
            }
            $refundPoints = min($refundPoints, $maxEarnedRefund);
            $creditmemo->setRefundEarnedPoints(max($refundPoints, 0));
            $creditmemo->setSimirewardpointsEarn($creditmemo->getRefundEarnedPoints());//Hai.Tran
        }
        //Brian allow creditmemo when creditmemo total equal zero
        if ($order->getSimirewardpointsSpent() > 0 
                && Mage::app()->getStore()->roundPrice($creditmemo->getGrandTotal()) <= 0 
        ) {
            $creditmemo->setAllowZeroGrandTotal(true);
        }
        
        return $this;
    }
    
    /**
     * Process creditmemo after save
     * 
     * @param type $observer
     * @return Simi_Simirewardpoints_Model_Observer
     */
    public function salesOrderCreditmemoSaveAfter($observer)
    {
        $creditmemo = $observer['creditmemo'];
        $order      = $creditmemo->getOrder();
        
        // Refund spent points
        if ($creditmemo->getRefundSpentPoints() > 0) {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            if ($customer->getId()) {
                Mage::helper('simirewardpoints/action')->addTransaction(
                    'spending_creditmemo', $customer, $creditmemo
                );
            }
        }
        
        // Deduce earned points
        if ($creditmemo->getRefundEarnedPoints() > 0) {
            if (empty($customer)) {
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            }
            if ($customer->getId()) {
                Mage::helper('simirewardpoints/action')->addTransaction(
                    'earning_creditmemo', $customer, $creditmemo
                );
            }
        }
        
        return $this;
    }
    
    function salesruleValidatorProcess($observer){ 
        $rule = $observer['rule'];        
        $needConvert = Mage::getStoreConfig('simirewardpoints/general/convert_point');
        if(!$needConvert) return $this;

        if ($rule->getCouponType() != Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON){
            $item = $observer['item'];
            $result = $observer['result'];
            $item->setDiscountAmountToPoint($item->getDiscountAmountToPoint() + $result['discount_amount']);
            $item->setBaseDiscountAmountToPoint($item->getBaseDiscountAmountToPoint() + $result['base_discount_amount']);
            $result['discount_amount'] = 0;
            $result['base_discount_amount'] = 0;
        }
        return $this;
    }
    function simirewardpointsTotalEarningCoupon($observer){
        $needConvert = Mage::getStoreConfig('simirewardpoints/general/convert_point');
        if(!$needConvert) return $this;
        
        $pointEarn = 0;
        $convertRate = Mage::getStoreConfig('simirewardpoints/general/convert_point_rate');
        $address = $observer['address'];
        foreach($address->getAllItems() as $item){
            $itemPoint = Mage::helper('simirewardpoints/calculator')->round($item->getBaseDiscountAmountToPoint() * $convertRate);
            $item->setSimirewardpointsEarn($item->getSimirewardpointsEarn() + $itemPoint);
            $pointEarn += $itemPoint;
        }
        if($pointEarn > 0){
            $address->setSimirewardpointsPointsByDiscount($pointEarn);
            $address->setSimirewardpointsEarn($address->getSimirewardpointsEarn() + $pointEarn);
        }
        return $this;
    }

    function salesOrderSaveBefore($observer){
        $order= $observer->getEvent()->getOrder();
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            $order->setSimirewardpointsEarn(0);
            foreach ($order->getAllItems() as $item){
                if ($item->getParentItemId())
                    continue;
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildrenItems() as $child) {
                        $child->setSimirewardpointsEarn(0);
                    }
                } elseif ($item->getProduct()) {
                    $item->setSimirewardpointsEarn(0);
                }
            }
            return $this;
        }
    }
}
