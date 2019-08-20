<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Model\Service;

use Vnecoms\VendorsSales\Model\Order;

class InvoiceService extends \Magento\Sales\Model\Service\InvoiceService
{
    /**
     * @param Order $order
     * @param array $qtys
     * @return \Magento\Sales\Model\Order\Invoice
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareVendorInvoice(Order $vendorOrder, array $qtys = [])
    {
        $order = $vendorOrder->getOrder();
        $invoice = $this->orderConverter->toInvoice($order);
        $totalQty = 0;
        $vendorOrderItems = $vendorOrder->getAllItems();
        
        foreach ($order->getAllItems() as $orderItem) {
            if (!$this->_canInvoiceItem($orderItem)) {
                continue;
            }
            $item = $this->orderConverter->itemToInvoiceItem($orderItem);
            if ($orderItem->isDummy()) {
                $qty = $orderItem->getQtyOrdered() ? $orderItem->getQtyOrdered() : 1;
            } elseif (isset($qtys[$orderItem->getId()])) {
                $qty = (double) $qtys[$orderItem->getId()];
            } else {
                $qty = $orderItem->getQtyToInvoice();
            }
            if (!isset($vendorOrderItems[$orderItem->getId()])) {
                $qty = 0;
            }
            $totalQty += $qty;
            $this->setInvoiceItemQuantity($item, $qty);
            $invoice->addItem($item);
        }

        $invoice->setVendorOrder($vendorOrder);
        $invoice->setTotalQty($totalQty);
        $invoice->collectTotals();

        $order->getInvoiceCollection()->addItem($invoice);
        return $invoice;
    }
}
