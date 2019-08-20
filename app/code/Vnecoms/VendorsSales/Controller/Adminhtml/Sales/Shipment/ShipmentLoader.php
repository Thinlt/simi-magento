<?php
/**
 * Copyright © Vnecoms. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Controller\Adminhtml\Sales\Shipment;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentTrackCreationInterface;
use Magento\Sales\Api\Data\ShipmentTrackCreationInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentItemCreationInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentItemCreationInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Vnecoms\VendorsSales\Model\Order\ShipmentFactory;
use Vnecoms\VendorsSales\Model\Order as VendorsOrder;
use Vnecoms\VendorsSales\Model\Order\ShipmentDocumentFactory;

/**
 * Class ShipmentLoader
 *
 * @package Vnecoms\VendorsSales\Controller\Adminhtml\Sales\Shipment
 * @method ShipmentLoader setOrderId($id)
 * @method ShipmentLoader setShipmentId($id)
 * @method ShipmentLoader setShipment($shipment)
 * @method ShipmentLoader setTracking($tracking)
 * @method int getOrderId()
 * @method int getShipmentId()
 * @method array getTracking()
 * @method VendorsOrder getVendorOrder()
 */
class ShipmentLoader extends DataObject
{
    const SHIPMENT = 'shipment';

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackFactory
     */
    private $trackFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ShipmentItemCreationInterfaceFactory
     */
    private $itemFactory;

    /**
     * @var ShipmentDocumentFactory
     */
    private $shipDocumentFactory;


    /**
     * ShipmentLoader constructor.
     * @param ManagerInterface $messageManager
     * @param Registry $registry
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param ShipmentFactory $shipmentFactory
     * @param TrackFactory $trackFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param ShipmentItemCreationInterfaceFactory $itemFactory
     * @param ShipmentDocumentFactory $shipDocumentFactory
     * @param array $data
     */
    public function __construct(
        ManagerInterface $messageManager,
        Registry $registry,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentFactory $shipmentFactory,
        TrackFactory $trackFactory,
        OrderRepositoryInterface $orderRepository,
        ShipmentItemCreationInterfaceFactory $itemFactory,
        ShipmentDocumentFactory $shipDocumentFactory,
        array $data = []
    ) {
        $this->messageManager = $messageManager;
        $this->registry = $registry;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentFactory = $shipmentFactory;
        $this->trackFactory = $trackFactory;
        $this->orderRepository = $orderRepository;
        $this->itemFactory = $itemFactory;
        $this->shipDocumentFactory = $shipDocumentFactory;
        parent::__construct($data);
    }

    /**
     * Initialize shipment items QTY
     *
     * @return array
     */
    protected function getItemQtys()
    {
        $data = $this->getShipment();

        return isset($data['items']) ? $data['items'] : [];
    }


    /**
     * Initialize shipment model instance
     *
     * @param void
     * @return bool|\Magento\Sales\Model\Order\Shipment
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function load()
    {
        $shipment = false;
        $orderId = $this->getOrderId();
        $shipmentId = $this->getShipmentId();
        if ($shipmentId) {
            $shipment = $this->shipmentRepository->get($shipmentId);
        } elseif ($orderId) {
            $vendorOrder = $this->getVendorOrder();

            $order = $this->orderRepository->get($orderId);

            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->messageManager->addErrorMessage(__('The order no longer exists.'));
                return false;
            }
            /**
             * Check shipment is available to create separate from invoice
             */
            if ($order->getForcedShipmentWithInvoice()) {
                $this->messageManager->addErrorMessage(__('Cannot do shipment for the order separately from invoice.'));
                return false;
            }
            /**
             * Check shipment create availability
             */
            if (!$vendorOrder->canShip()) {
                $this->messageManager->addErrorMessage(__('Cannot do shipment for the order.'));
                return false;
            }

            $shipmentItems = $this->getShipmentItems($this->getShipment());

            $shipment = $this->shipDocumentFactory->create(
                $vendorOrder,
                $shipmentItems,
                $this->getTrackingArray()
            );
        }

        $this->registry->register('current_shipment', $shipment);
        return $shipment;
    }

    /**
     * Convert UI-generated tracking array to Data Object array
     *
     * @return ShipmentTrackCreationInterface[]
     * @throws LocalizedException
     */
    private function getTrackingArray()
    {
        $tracks = $this->getTracking() ?: [];
        $trackingCreation = [];
        foreach ($tracks as $track) {
            if (!isset($track['number']) || !isset($track['title']) || !isset($track['carrier_code'])) {
                throw new LocalizedException(
                    __('Tracking information must contain title, carrier code, and tracking number')
                );
            }
            /** @var ShipmentTrackCreationInterface $trackCreation */
            $trackCreation = $this->trackFactory->create();
            $trackCreation->setTrackNumber($track['number']);
            $trackCreation->setTitle($track['title']);
            $trackCreation->setCarrierCode($track['carrier_code']);
            $trackingCreation[] = $trackCreation;
        }

        return $trackingCreation;
    }

    /**
     * Extract product id => product quantity array from shipment data.
     *
     *  arrayShipmentItemsData = [
     *   [itemId] => qty,
     *   [itemdId2] => qty
     * ]
     * @param array $shipmentData
     * @return int[]
     */
    private function getShipmentItems(array $shipmentData)
    {
        $shipmentItems = [];
        $itemQty = isset($shipmentData['items']) ? $shipmentData['items'] : [];
        foreach ($itemQty as $itemId => $quantity) {
            /** @var ShipmentItemCreationInterface $item */
            $item = $this->itemFactory->create();
            $item->setOrderItemId($itemId);
            $item->setQty($quantity);
            $shipmentItems[] = $item;
        }
        return $shipmentItems;
    }

    /**
     * Retrieve shipment
     *
     * @return array
     */
    public function getShipment()
    {
        return $this->getData(self::SHIPMENT) ?: [];
    }
}
