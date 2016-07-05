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
 * Simirewardpoints Spend for Order by Point Model
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Model_Total_Creditmemo_Point extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect total when create Creditmemo
     * 
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $creditmemo->setSimirewardpointsDiscount(0);
        $creditmemo->setSimirewardpointsBaseDiscount(0);

        $order = $creditmemo->getOrder();
        
        if ($order->getSimirewardpointsDiscount() < 0.0001) {
            return ;
        }

        $totalDiscountAmount = 0;
        $baseTotalDiscountAmount = 0;
        $baseTotalDiscountRefunded = 0;
        $totalDiscountRefunded = 0;
        foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
            if ($existedCreditmemo->getSimirewardpointsDiscount()) {
                $totalDiscountRefunded     += $existedCreditmemo->getSimirewardpointsDiscount();
                $baseTotalDiscountRefunded += $existedCreditmemo->getSimirewardpointsBaseDiscount();
            }
        }

        /**
         * Calculate how much shipping discount should be applied
         * basing on how much shipping should be refunded.
         */
        $baseShippingAmount = $creditmemo->getBaseShippingAmount();
        if ($baseShippingAmount) {
            $baseTotalDiscountAmount = $baseShippingAmount * $order->getSimirewardpointsBaseAmount() / $order->getBaseShippingAmount();
            $totalDiscountAmount = $order->getShippingAmount() * $baseTotalDiscountAmount / $order->getBaseShippingAmount();
        }
        
        if ($this->isLast($creditmemo)) {
            $baseTotalDiscountAmount   = $order->getSimirewardpointsBaseDiscount() - $baseTotalDiscountRefunded;
            $totalDiscountAmount       = $order->getSimirewardpointsDiscount() - $totalDiscountRefunded;
        } else {
            /** @var $item Mage_Sales_Model_Order_Invoice_Item */
            foreach ($creditmemo->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }
                $orderItemDiscount      = (float) $orderItem->getSimirewardpointsDiscount()*$orderItem->getQtyInvoiced()/$orderItem->getQtyOrdered();
                $baseOrderItemDiscount  = (float) $orderItem->getSimirewardpointsBaseDiscount()*$orderItem->getQtyInvoiced()/$orderItem->getQtyOrdered();
                
                $orderItemQty           = $orderItem->getQtyInvoiced();

                if ($orderItemDiscount && $orderItemQty) {                    
                    $totalDiscountAmount += $creditmemo->roundPrice($orderItemDiscount / $orderItemQty * $item->getQty(), 'regular', true);
                    $baseTotalDiscountAmount += $creditmemo->roundPrice($baseOrderItemDiscount / $orderItemQty * $item->getQty(), 'base', true);
                }
            }
        }

        $creditmemo->setSimirewardpointsDiscount($totalDiscountAmount);
        $creditmemo->setSimirewardpointsBaseDiscount($baseTotalDiscountAmount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmount);// + $totalHiddenTax);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmount);// + $baseTotalHiddenTax);
        return $this;
    }
    
    /**
     * check credit memo is last or not
     * 
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return boolean
     */
    public function isLast($creditmemo)
    {
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }
}
