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
 * Simirewardpoints Total Point Earn/Spend Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Adminhtml_Totals_Creditmemo_Rewards extends Mage_Adminhtml_Block_Template
{
    /**
     * get current creditmemo
     * 
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }
    
    /**
     * check admin can refund point that customer spent
     * 
     * @return boolean
     */
    public function canRefundPoints()
    {
        if ($this->getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        if ($this->getMaxPointRefund()) {
            return true;
        }
        return false;
    }
    
    /**
     * max point that admin can refund to customer
     * 
     * @return int
     */
    public function getMaxPointRefund()
    {
        if ($this->hasData('max_point_refund')) {
            return $this->getData('max_point_refund');
        }
        $maxPointRefund = 0;
        if ($creditmemo = $this->getCreditmemo()) {
            $order = $creditmemo->getOrder();
            
            $maxPoint = $order->getSimirewardpointsSpent();
            $maxPointRefund = $maxPoint - (int)Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('action', 'spending_creditmemo')
                ->addFieldToFilter('order_id', $order->getId())
                ->getFieldTotal();
            if ($creditmemo->getSimirewardpointsDiscount()) {
                $currentPoint = ceil($maxPoint * $creditmemo->getSimirewardpointsDiscount() / $order->getSimirewardpointsDiscount());
            } else {
                $currentPoint = 0;
            }
            $this->setData('total_point', $maxPoint);
            $this->setData('current_point', min($currentPoint, $maxPointRefund));
        }
        $this->setData('max_point_refund', $maxPointRefund);
        return $this->getData('max_point_refund');
    }
    
    /**
     * get current refund points for this credit memo
     * 
     * @return int
     */
    public function getCurrentPoint()
    {
        if (!$this->hasData('max_point_refund')) {
            $this->getMaxPointRefund();
        }
        return (int)$this->getData('current_point');
    }
    
    /**
     * check admin can refund earned point of customer
     * (deduct point from customer points balance)
     * 
     * @return boolean
     */
    public function canRefundEarnedPoints()
    {
        if ($this->getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        if ($this->getMaxEarnedRefund()) {
            return true;
        }
        return false;
    }
    
    /**
     * get max point can deduct from customer balance
     * 
     * @return int
     */
    public function getMaxEarnedRefund()
    {
        if (!$this->hasData('max_earned_refund')) {
            $maxEarnedRefund = 0;
            $earnPoint = 0;
            if ($creditmemo = $this->getCreditmemo()) {
                $order = $creditmemo->getOrder();
                
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
                
                foreach ($creditmemo->getAllItems() as $item) {
                    $orderItem = $item->getOrderItem();
                    if ($orderItem->isDummy()) {
                        continue;
                    }
                    $itemPoint  = (int)$orderItem->getSimirewardpointsEarn();
                    $itemPoint  = $itemPoint * $item->getQty() / $orderItem->getQtyOrdered();
                    $earnPoint += floor($itemPoint);
                }
                // Hiepdd add shipping earned points
                if($order->getCreditmemosCollection()->getSize() == 0) {
                    $earnPoint += Mage::helper('simirewardpoints/calculation_earning')->getShippingEarningPoints($order);                    
                }
                // end
              //  if(!$this->isLast($creditmemo) && $maxEarnedRefund >= $earnPoint) 
                    $maxEarnedRefund = $earnPoint;             
            }
            $this->setData('max_earned_refund', $maxEarnedRefund);
        }
        return $this->getData('max_earned_refund');
    }
    public function isLast($creditmemo)
    {
        foreach ($creditmemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }
    public function isFirstCreditmemo($order){
        foreach ($order->getCreditmemoCollection() as $creditmemo){
            
        }
    }
}
