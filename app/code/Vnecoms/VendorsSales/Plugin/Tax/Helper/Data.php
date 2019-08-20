<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Plugin\Tax\Helper;

use Magento\Sales\Model\EntityInterface;
use Vnecoms\VendorsSales\Model\Order as VendorOrder;
use Vnecoms\VendorsSales\Model\Order\Invoice as VendorInvoice;
use Magento\Tax\Api\Data\OrderTaxDetailsItemInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Creditmemo;

class Data extends \Magento\Tax\Helper\Data
{
	 /**
     * Check if resource for which access is needed has self permissions defined in webapi config.
     *
     * @param \Magento\Framework\Authorization $subject
     * @param callable $proceed
     * @param string $privilege
     *
     * @return bool true If resource permission is self, to allow
     * customer access without further checks in parent method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetCalculatedTaxes(
        \Magento\Tax\Helper\Data $subject,
        \Closure $proceed,
        $source
    ) {

    	$taxClassAmount = [];
        if (empty($source)) {
            return $taxClassAmount;
        }
        $current = $source;

        if ($source instanceof Invoice || $source instanceof Creditmemo || $source instanceof VendorOrder) {
            $source = $current->getOrder();
        }

        if ($source instanceof VendorInvoice) {
            $source = $current->getOrder()->getOrder();
        }

        if ($current == $source) {
            $taxClassAmount = $this->calculateTaxForOrder($current);
        } else {

            if ($current instanceof VendorInvoice || $current instanceof VendorOrder) {
                $taxClassAmount = $this->calculateTaxForVendorItems($source, $current);
            }else{
                $taxClassAmount = $this->calculateTaxForItems($source, $current);
            }

        }



        foreach ($taxClassAmount as $key => $tax) {
            $taxClassAmount[$key]['tax_amount'] = $this->priceCurrency->round($tax['tax_amount']);
            $taxClassAmount[$key]['base_tax_amount'] = $this->priceCurrency->round($tax['base_tax_amount']);
        }

        return array_values($taxClassAmount);
    }


    /**
     * @param  VendorOrder $order
     * @param  $salesItem
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function calculateTaxForVendorItems(EntityInterface $order, $salesItem)
    {
        $taxClassAmount = [];


        $orderTaxDetails = $this->orderTaxManagement->getOrderTaxDetails($order->getId());

        // Apply any taxes for the items
        /** @var $item \Magento\Sales\Model\Order\Invoice\Item|\Magento\Sales\Model\Order\Creditmemo\Item|\Magento\Sales\Model\Order\Item */

        $items = $salesItem->getItems() ? $salesItem->getItems() : $salesItem->getAllItems();

        foreach ($items as $item) {
            $orderItem = $item->getOrderItem();
            if($orderItem->getVendorId() != $order->getVendorId()) continue;
            $orderItemId = $orderItem->getId();
            $orderItemTax = $orderItem->getTaxAmount();
            $itemTax = $item->getTaxAmount();
            if (!$itemTax || !floatval($orderItemTax)) {
                continue;
            }
            //An invoiced item or credit memo item can have a different qty than its order item qty
            $itemRatio = $itemTax / $orderItemTax;
            $itemTaxDetails = $orderTaxDetails->getItems();
            foreach ($itemTaxDetails as $itemTaxDetail) {
               //Aggregate taxable items associated with an item
               if ($itemTaxDetail->getItemId() == $orderItemId) {
                   $taxClassAmount = $this->_aggregateTaxes($taxClassAmount, $itemTaxDetail, $itemRatio);
               } elseif ($itemTaxDetail->getAssociatedItemId() == $orderItemId) {
                   $taxableItemType = $itemTaxDetail->getType();
                   $ratio = $itemRatio;
                   if ($item->getTaxRatio()) {
                       $taxRatio = unserialize($item->getTaxRatio());
                       if (isset($taxRatio[$taxableItemType])) {
                           $ratio = $taxRatio[$taxableItemType];
                       }
                   }
                   $taxClassAmount = $this->_aggregateTaxes($taxClassAmount, $itemTaxDetail, $ratio);
               }
            }
        }
        // Apply any taxes for shipping
        $shippingTaxAmount = $salesItem->getShippingTaxAmount();
        $originalShippingTaxAmount = $order->getShippingTaxAmount();

        if ($shippingTaxAmount && $originalShippingTaxAmount &&
            $shippingTaxAmount != 0 && floatval($originalShippingTaxAmount)
        ) {
            //An invoice or credit memo can have a different qty than its order
            $shippingRatio = $shippingTaxAmount / $originalShippingTaxAmount;

            $itemTaxDetails = $orderTaxDetails->getItems();

            foreach ($itemTaxDetails as $itemTaxDetail) {
                //Aggregate taxable items associated with shipping
                if ($itemTaxDetail->getType() == \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING) {
                    $taxClassAmount = $this->_aggregateTaxes($taxClassAmount, $itemTaxDetail, $shippingRatio);
                }
            }
        }

        return $taxClassAmount;
    }

    /**
     * Accumulates the pre-calculated taxes for each tax class
     *
     * This method accepts and returns the 'taxClassAmount' array with format:
     * array(
     *  $index => array(
     *      'tax_amount'        => $taxAmount,
     *      'base_tax_amount'   => $baseTaxAmount,
     *      'title'             => $title,
     *      'percent'           => $percent
     *  )
     * )
     *
     * @param  array                        $taxClassAmount
     * @param  OrderTaxDetailsItemInterface $itemTaxDetail
     * @param  float                        $ratio
     * @return array
     */
    private function _aggregateTaxes($taxClassAmount, OrderTaxDetailsItemInterface $itemTaxDetail, $ratio)
    {
        $itemAppliedTaxes = $itemTaxDetail->getAppliedTaxes();
        foreach ($itemAppliedTaxes as $itemAppliedTax) {
            $taxAmount = $itemAppliedTax->getAmount() * $ratio;
            $baseTaxAmount = $itemAppliedTax->getBaseAmount() * $ratio;

            if (0 == $taxAmount && 0 == $baseTaxAmount) {
                continue;
            }
            $taxCode = $itemAppliedTax->getCode();
            if (!isset($taxClassAmount[$taxCode])) {
                $taxClassAmount[$taxCode]['title'] = $itemAppliedTax->getTitle();
                $taxClassAmount[$taxCode]['percent'] = $itemAppliedTax->getPercent();
                $taxClassAmount[$taxCode]['tax_amount'] = $taxAmount;
                $taxClassAmount[$taxCode]['base_tax_amount'] = $baseTaxAmount;
            } else {
                $taxClassAmount[$taxCode]['tax_amount'] += $taxAmount;
                $taxClassAmount[$taxCode]['base_tax_amount'] += $baseTaxAmount;
            }
        }

        return $taxClassAmount;
    }
}