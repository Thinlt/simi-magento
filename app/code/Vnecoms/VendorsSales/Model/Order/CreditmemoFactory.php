<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Model\Order;

/**
 * Factory class for @see \Magento\Sales\Api\Data\ShipmentInterface
 */
class CreditmemoFactory extends \Magento\Sales\Model\Order\CreditmemoFactory
{
    /**
     * Prepare order creditmemo based on order items and requested params
     *
     * @param \Vnecoms\VendorsSales\Model\Order $order
     * @param array $data
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function createByVendorOrder(\Vnecoms\VendorsSales\Model\Order $vendorOrder, array $data = [])
    {
        $order = $vendorOrder->getOrder();
        $totalQty = 0;
        $creditmemo = $this->convertor->toCreditmemo($order);
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $module = $object_manager->get('Magento\Framework\Module\Manager');

        if ($module->isEnabled("Vnecoms_VendorsShipping")) {
            // to do something
        } else {
            $creditmemo->setShippingAmount(0);
            $creditmemo->setBaseShippingAmount(0);
            $creditmemo->setShippingTaxAmount(0);
            $creditmemo->setBaseShippingInclTax(0);
            $creditmemo->setBaseShippingTaxAmount(0);
            $creditmemo->setShippingInclTax(0);
        }

        $qtys = isset($data['qtys']) ? $data['qtys'] : [];

        foreach ($order->getAllItems() as $orderItem) {
            if (!$this->canRefundItem($orderItem, $qtys)) {
                continue;
            }

            $item = $this->convertor->itemToCreditmemoItem($orderItem);
            if ($orderItem->isDummy()) {
                $qty = 1;
                $orderItem->setLockedDoShip(true);
            } else {
                if (isset($qtys[$orderItem->getId()])) {
                    $qty = (double)$qtys[$orderItem->getId()];
                } elseif (!count($qtys)) {
                    $qty = $orderItem->getQtyToRefund();
                } else {
                    continue;
                }
            }
            if ($vendorOrder->getVendorId() != $orderItem->getVendorId()) {
                $qty = 0;
            }
            $totalQty += $qty;
            $item->setQty($qty);
            $creditmemo->addItem($item);
        }

        $creditmemo->setVendorOrderId($vendorOrder->getId());
        $creditmemo->setTotalQty($totalQty);
        $this->initData($creditmemo, $data);
        $creditmemo->collectTotals();
        return $creditmemo;
    }
}
