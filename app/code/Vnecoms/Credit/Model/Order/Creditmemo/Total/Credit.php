<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\Order\Creditmemo\Total;

class Credit extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemo->setCreditAmount(0);
        $creditmemo->setBaseCreditAmount(0);

        $totalCreditAmount = 0;
        $baseTotalCreditAmount = 0;


        /** @var $item \Magento\Sales\Model\Order\Invoice\Item */
        foreach ($creditmemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();

            if ($orderItem->isDummy()) {
                continue;
            }
            
            $orderItemCredit = (double)$orderItem->getCreditInvoiced();
            $baseOrderItemCredit = (double)$orderItem->getBaseCreditInvoiced();
            $orderItemQty = $orderItem->getQtyInvoiced();

            if ($orderItemCredit && $orderItemQty) {
                $credit = $orderItemCredit - $orderItem->getCreditRefunded();
                $baseCredit = $baseOrderItemCredit - $orderItem->getBaseCreditRefunded();
                if (!$item->isLast()) {
                    $availableQty = $orderItemQty - $orderItem->getQtyRefunded();
                    $credit = $creditmemo->roundPrice($credit / $availableQty * $item->getQty(), 'regular', true);
                    $baseCredit = $creditmemo->roundPrice(
                        $baseCredit / $availableQty * $item->getQty(),
                        'base',
                        true
                    );
                }

                $item->setCreditAmount($credit);
                $item->setBaseCreditAmount($baseCredit);

                $totalCreditAmount += $credit;
                $baseTotalCreditAmount += $baseCredit;
            }
        }

        $creditmemo->setCreditAmount(-$totalCreditAmount);
        $creditmemo->setBaseCreditAmount(-$baseTotalCreditAmount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalCreditAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalCreditAmount);
        return $this;
    }
}
