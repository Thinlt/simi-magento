<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Model\Sales\Order\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

/**
 * Order invoice shipping total calculation model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shipping extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {

        /*Create new vendor invoices for this invoice.*/
        $vendorInvoiceItems = $this->_getVendorInvoiceItem($invoice->getItems());

        $shippingAmount = 0;
        $baseShippingAmount = 0;
        $shippingInclTaxs = 0;
        $baseShippingInclTaxs = 0;

        foreach ($vendorInvoiceItems as $vendorId => $items) {
            $includeShippingTax = true;
            $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
            $vendorOrder = $object_manager->get('\Vnecoms\VendorsSales\Model\Order');
            $vendorOrderId = $vendorOrder->getResource()->getVendorOrderId($vendorId, $invoice->getOrder()->getId());
            $vendorOrder->load($vendorOrderId);
            /**
             * Check shipping amount in previous invoices
             */
            foreach ($vendorOrder->getInvoiceCollection() as $previousInvoice) {
                if ($previousInvoice->getShippingAmount() && !$previousInvoice->isCanceled()) {
                    $includeShippingTax= false;
                }
            }
            if ($includeShippingTax) {
                $shippingAmount += $vendorOrder->getShippingAmount();
                $baseShippingAmount += $vendorOrder->getBaseShippingAmount();
                $shippingInclTaxs += $vendorOrder->getShippingInclTax();
                $baseShippingInclTaxs += $vendorOrder->getBaseShippingInclTax();
            }
        }

        //var_dump($shippingAmount);exit;
        //echo $shippingAmount;exit;

        $invoice->setShippingAmount(0);
        $invoice->setBaseShippingAmount(0);
        $orderShippingAmount = $shippingAmount;
        $baseOrderShippingAmount = $baseShippingAmount;
        $shippingInclTax = $shippingInclTaxs;
        $baseShippingInclTax = $baseShippingInclTaxs;

        if ($orderShippingAmount > 0) {
            $invoice->setShippingAmount($orderShippingAmount);
            $invoice->setBaseShippingAmount($baseOrderShippingAmount);
            $invoice->setShippingInclTax($shippingInclTax);
            $invoice->setBaseShippingInclTax($baseShippingInclTax);
            $invoice->setGrandTotal($invoice->getGrandTotal() + $orderShippingAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseOrderShippingAmount);
        }

        return $this;
    }

    /**
     * get vendor id from invoice
     * @param $items
     * @return array
     */
    protected function _getVendorInvoiceItem($items)
    {

        /*Create new vendor invoices for this invoice.*/
        $vendorInvoiceItems = [];

        $qtyChildItems = [];
        // get all child item

        foreach ($items as $item) {
            $orderItem = $item->getOrderItem();
            if (!$item->getParentItem() && !$orderItem->getParentItem()) {
                continue;
            }
            $qtyChildItems[$orderItem->getId()] = $item->getQty();
        }

        //get vendor array from invoice item

        foreach ($items as $item) {
            $orderItem = $item->getOrderItem();
            if ($item->getParentItem() || $orderItem->getParentItem() || $item->getQty() <= 0) {
                continue;
            }

            $checkBundle = true;
            if ($orderItem->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                && $orderItem->getHasChildren()) {
                $checkBundle = false;
                foreach ($orderItem->getChildrenItems() as $child) {
                    if (isset($qtyChildItems[$child->getId()]) && $qtyChildItems[$child->getId()] > 0) {
                        $checkBundle = true;
                    }
                }
            }

            if ($orderItem->getVendorId() && $checkBundle) {
                if (!isset($vendorInvoiceItems[$orderItem->getVendorId()])) {
                    $vendorInvoiceItems[$orderItem->getVendorId()]=[];
                }
                $vendorInvoiceItems[$orderItem->getVendorId()][] = $item;
            }
        }

        return $vendorInvoiceItems;
    }
}
