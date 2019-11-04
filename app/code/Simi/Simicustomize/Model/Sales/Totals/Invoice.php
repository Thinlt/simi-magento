<?php

namespace Simi\Simicustomize\Model\Sales\Totals;

use Magento\Sales\Model\Order\Invoice as ModelInvoice;

/**
* Class Custom
* @package Simi\Simicustomize\Model\Total\Quote
*/
class Invoice extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
   /**
    * @param \Magento\Quote\Model\Quote $quote
    * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
    * @param \Magento\Quote\Model\Quote\Address\Total $total
    * @return $this|bool
    */
    public function collect(ModelInvoice $invoice)
    {
        parent::collect($invoice);
        $order = $invoice->getOrder();
        if ($order->getOrderType() == 'pre_order') {
            $baseGrandTotal = $order->getBaseGrandTotal();
            $grandTotal = $order->getGrandTotal();
            $invoice->setBaseGrandTotal($baseGrandTotal);
            $invoice->setGrandTotal($grandTotal);
        }
        return $this;
    }
}