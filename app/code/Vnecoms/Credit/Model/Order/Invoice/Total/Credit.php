<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\Order\Invoice\Total;

class Credit extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $items = $invoice->getItems();
        if (!count($items)) {
            return $this;
        }
        
        $totalCreditAmount = 0;
        $baseTotalCreditAmount = 0;
        foreach($items as $item){
        $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }

            $orderItemCredit = (double)$orderItem->getCreditAmount();
            $baseOrderItemCredit = (double)$orderItem->getBaseCreditAmount();
            $orderItemQty = $orderItem->getQtyOrdered();

            if ($orderItemCredit && $orderItemQty) {
                /**
                 * Resolve rounding problems
                 */
                $credit = $orderItemCredit - $orderItem->getCreditInvoiced();
                $baseCredit = $baseOrderItemCredit - $orderItem->getBaseCreditInvoiced();
                if (!$item->isLast()) {
                    $activeQty = $orderItemQty - $orderItem->getQtyInvoiced();
                    $credit = $invoice->roundPrice($credit / $activeQty * $item->getQty(), 'regular', true);
                    $baseCredit = $invoice->roundPrice($baseCredit / $activeQty * $item->getQty(), 'base', true);
                }

                $item->setCreditAmount($credit);
                $item->setBaseCreditAmount($baseCredit);

                $totalCreditAmount += $credit;
                $baseTotalCreditAmount += $baseCredit;
            }
        }
        $invoice->setCreditAmount(-$totalCreditAmount);
        $invoice->setBaseCreditAmount(-$baseTotalCreditAmount);

        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalCreditAmount);
        
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalCreditAmount);
        return $this;
    }
}
