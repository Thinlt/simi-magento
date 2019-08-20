<?php
/**
 * Copyright © Vnecoms. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Model\Order;

/**
 * Factory class for @see \Magento\Sales\Api\Data\ShipmentInterface
 */
class ShipmentFactory extends \Magento\Sales\Model\Order\ShipmentFactory
{
    /**
     * Creates shipment instance with specified parameters.
     *
     * @param \Vnecoms\VendorsSales\Model\Order $order
     * @param array $items
     * @param array|null $tracks
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     */
    public function createVendorShipment(\Vnecoms\VendorsSales\Model\Order $vendorOrder, array $items = [], $tracks = null)
    {
        $order = $vendorOrder->getOrder();
        $shipment = $this->prepareVendorsItems($this->converter->toShipment($order), $vendorOrder, $items);
        if ($tracks) {
            $shipment = $this->prepareTracks($shipment, $tracks);
        }
        return $shipment;
    }

    /**
     * Adds items to the shipment.
     *
     * @param \Magento\Sales\Api\Data\ShipmentInterface $shipment
     * @param \Vnecoms\VendorsSales\Model\Order $vendorOrder
     * @param array $items
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     */
    protected function prepareVendorsItems(
        \Magento\Sales\Api\Data\ShipmentInterface $shipment,
        \Vnecoms\VendorsSales\Model\Order $vendorOrder,
        array $items = []
    ) {
        $shipmentItems = [];
        $order = $vendorOrder->getOrder();
        foreach ($order->getAllItems() as $orderItem) {
            if ($this->validateItem($orderItem, $vendorOrder, $items) === false) {
                continue;
            }

            /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
            $item = $this->converter->itemToShipmentItem($orderItem);
            if ($orderItem->getIsVirtual() || ($orderItem->getParentItemId() && !$orderItem->isShipSeparately())) {
                $item->isDeleted(true);
            }

            if ($orderItem->isDummy(true)) {
                $qty = 0;

                if (isset($items[$orderItem->getParentItemId()])) {
                    $productOptions = $orderItem->getProductOptions();

                    if (isset($productOptions['bundle_selection_attributes'])) {
                        if (interface_exists('Magento\Framework\Serialize\SerializerInterface')) {
                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            $serializer = $objectManager->create(\Magento\Framework\Serialize\SerializerInterface::class);
                            $bundleSelectionAttributes = $serializer->unserialize($productOptions['bundle_selection_attributes']);
                        } else if (!interface_exists('Magento\Framework\Serialize\SerializerInterface')) {
                            $bundleSelectionAttributes = unserialize($productOptions['bundle_selection_attributes']);
                        }

                        if ($bundleSelectionAttributes) {
                            $qty = $bundleSelectionAttributes['qty'] * $items[$orderItem->getParentItemId()];
                            $qty = min($qty, $orderItem->getSimpleQtyToShip());

                            $item->setQty($this->castQty($orderItem, $qty));
                            $shipmentItems[] = $item;
                            continue;
                        } else {
                            $qty = 1;
                        }
                    }
                } else {
                    $qty = 1;
                }
            } else {
                if (isset($items[$orderItem->getId()])) {
                    $qty = min($items[$orderItem->getId()], $orderItem->getQtyToShip());
                } elseif (!count($items)) {
                    $qty = $orderItem->getQtyToShip();
                } else {
                    continue;
                }
            }

            $item->setQty($this->castQty($orderItem, $qty));
            $shipmentItems[] = $item;
        }
        return $this->setItemsToShipment($shipment, $shipmentItems);
    }

    /**
     * Validate order item before shipment
     *
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param \Vnecoms\VendorsSales\Model\Order $vendorOrder
     * @param array $items
     * @return bool
     */
    private function validateItem(\Magento\Sales\Model\Order\Item $orderItem, $vendorOrder, array $items)
    {
        if (!$this->canShipItem($orderItem, $items)) {
            return false;
        }

        /**
         * If the item is not item of current vendor just not show it
         */
        if ($vendorOrder->getVendorId() != $orderItem->getVendorId()) {
            return false;
        }

        // Remove from shipment items without qty or with qty=0
        if (!$orderItem->isDummy(true)
            && (!isset($items[$orderItem->getId()]) || $items[$orderItem->getId()] <= 0)
        ) {
            return false;
        }
        return true;
    }

    /**
     * Set prepared items to shipment document
     *
     * @param \Magento\Sales\Api\Data\ShipmentInterface $shipment
     * @param array $shipmentItems
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     */
    private function setItemsToShipment(\Magento\Sales\Api\Data\ShipmentInterface $shipment, $shipmentItems)
    {
        $totalQty = 0;

        /**
         * Verify that composite products shipped separately has children, if not -> remove from collection
         */
        /** @var \Magento\Sales\Model\Order\Shipment\Item $shipmentItem */
        foreach ($shipmentItems as $key => $shipmentItem) {
            if ($shipmentItem->getOrderItem()->getHasChildren()
                && $shipmentItem->getOrderItem()->isShipSeparately()
            ) {
                $containerId = $shipmentItem->getOrderItem()->getId();
                $childItems = array_filter($shipmentItems, function ($item) use ($containerId) {
                    return $containerId == $item->getOrderItem()->getParentItemId();
                });

                if (count($childItems) <= 0) {
                    unset($shipmentItems[$key]);
                    continue;
                }
            }
            $totalQty += $shipmentItem->getQty();
            $shipment->addItem($shipmentItem);
        }
        return $shipment->setTotalQty($totalQty);
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @param string|int|float $qty
     * @return float|int
     */
    private function castQty(\Magento\Sales\Model\Order\Item $item, $qty)
    {
        if ($item->getIsQtyDecimal()) {
            $qty = (double)$qty;
        } else {
            $qty = (int)$qty;
        }

        return $qty > 0 ? $qty : 0;
    }
}
