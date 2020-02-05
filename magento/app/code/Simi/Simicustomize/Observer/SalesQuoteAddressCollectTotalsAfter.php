<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SalesQuoteAddressCollectTotalsAfter implements ObserverInterface
{
    /**
     *  \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\Order $order
    ) {
        $this->objectManager = $objectManager;
        $this->order = $order;
    }

    public function execute(Observer $observer) {
//        $quote = $observer->getEvent()->getQuote();
//        $total = $observer->getEvent()->getTotal();
//        $shippingAssignment = $observer->getEvent()->getShippingAssignment();
//        /**
//         * check is pre-order #2 (repay)
//         * Remove tax
//         */
//        if ($quote->getReservedOrderId()) {
//            $order = $this->order->loadByIncrementId($quote->getReservedOrderId());
//            if ($order->getId() && $order->getDepositAmount()) {
//                $tax = $total->getTaxAmount();
//                $baseTax = $total->getBaseTaxAmount();
//                $grandTotal = max($total->getGrandTotal() - $tax, 0);
//                $baseGrandTotal = max($total->getBaseGrandTotal() - $baseTax, 0);
//                $total->setTotalAmount('tax', 0);
//                $total->setBaseTotalAmount('tax', 0);
//                $total->setTaxAmount(0);
//                $total->setTotalAmount('grand_total', $grandTotal);
//                $total->setBaseTotalAmount('grand_total', $baseGrandTotal);
//                $total->setGrandTotal($grandTotal);
//                $total->setBaseGrandTotal($baseGrandTotal);
//            }
//        }
    }
}
