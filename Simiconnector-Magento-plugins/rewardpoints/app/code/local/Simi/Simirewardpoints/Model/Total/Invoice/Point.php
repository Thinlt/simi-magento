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
class Simi_Simirewardpoints_Model_Total_Invoice_Point extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect total when create Invoice
     * 
     * @param Mage_Sales_Model_Order_Invoice $invoice
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $invoiceCollection = $order->getInvoiceCollection();

        /** Earning point **/
        $earnPoint = 0;
        $maxEarn  = $order->getSimirewardpointsEarn();
        $maxEarn -= (int)Mage::getResourceModel('simirewardpoints/transaction_collection')
            ->addFieldToFilter('action', 'earning_invoice')
            ->addFieldToFilter('order_id', $order->getId())
            ->getFieldTotal();
        if ($maxEarn >= 0) {
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy())
                    continue;
                $earnPoint += floor((int)$orderItem->getSimirewardpointsEarn() * $item->getQty() / $orderItem->getQtyOrdered());
            }
            if($invoiceCollection->getSize() == 0) 
                $earnPoint += Mage::helper('simirewardpoints/calculation_earning')->getShippingEarningPoints($order);
            if($this->isLast($invoice)) 
                $earnPoint = $maxEarn;
        }
        if($earnPoint > 0)
            $invoice->setSimirewardpointsEarn($earnPoint);
        /** End earningn point **/
        
        /** Spending point **/
        if ($order->getSimirewardpointsDiscount() < 0.0001) {
            return ;
        }

        $totalDiscountAmount     = 0;
        $baseTotalDiscountAmount = 0;        
        $totalDiscountInvoiced     = 0;
        $baseTotalDiscountInvoiced = 0;

        /**
         * Checking if shipping discount was added in previous invoices.
         * So basically if we have invoice with positive discount and it
         * was not canceled we don't add shipping discount to this one.
         */
        $addShippingDicount = true;
        foreach ($invoiceCollection as $previusInvoice) {
            if ($previusInvoice->getSimirewardpointsDiscount()) {
                $addShippingDicount = false;
                $totalDiscountInvoiced     += $previusInvoice->getSimirewardpointsDiscount();
                $baseTotalDiscountInvoiced += $previusInvoice->getSimirewardpointsBaseDiscount();
            }
        }
        if ($addShippingDicount) {
            $totalDiscountAmount     = $order->getSimirewardpointsAmount();
            $baseTotalDiscountAmount = $order->getSimirewardpointsBaseAmount();
        }        
        if ($this->isLast($invoice)) {
            $totalDiscountAmount     = $order->getSimirewardpointsDiscount() - $totalDiscountInvoiced;
            $baseTotalDiscountAmount = $order->getSimirewardpointsBaseDiscount() - $baseTotalDiscountInvoiced;
        } else {
            /** @var $item Mage_Sales_Model_Order_Invoice_Item */
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                     continue;
                }
                $orderItemDiscount      = (float) $orderItem->getSimirewardpointsDiscount();
                $baseOrderItemDiscount  = (float) $orderItem->getSimirewardpointsBaseDiscount();
                $orderItemQty       = $orderItem->getQtyOrdered();
                if ($orderItemDiscount && $orderItemQty) {
                    $totalDiscountAmount += $invoice->roundPrice($orderItemDiscount / $orderItemQty * $item->getQty(), 'regular', true);
                    $baseTotalDiscountAmount += $invoice->roundPrice($baseOrderItemDiscount / $orderItemQty * $item->getQty(), 'base', true);
                }
            }
        }
        
        $invoice->setSimirewardpointsDiscount($totalDiscountAmount);
        $invoice->setSimirewardpointsBaseDiscount($baseTotalDiscountAmount);

        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmount);
        /** End spending point **/
        return $this;
    }
    public function isLast($invoice){
        foreach ($invoice->getAllItems() as $item) {
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
}
