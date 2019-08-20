<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Vnecoms\VendorsSales\Model\Order\Email\Sender\ShipmentSender;

class ProcessShipment implements ObserverInterface
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;



    /**
     * @var \Vnecoms\VendorsSales\Model\OrderFactory
     */
    protected $_vendorOrderFactory;

    /**
     * @var ShipmentSender
     */
    protected $_shipmentSender;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        \Vnecoms\VendorsSales\Model\OrderFactory $vendorOrderFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        \Vnecoms\VendorsSales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
    ) {
        $this->_vendorOrderFactory = $vendorOrderFactory;
        $this->_eventManager = $eventManager;
        $this->_objectManager =$objectManagerInterface;
        $this->_vendorHelper = $vendorHelper;
        $this->_shipmentSender = $shipmentSender;
    }

    /**
     * Add multiple vendor order row for each vendor.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* Do nothing if the extension is not enabled.*/
        if (!$this->_vendorHelper->moduleEnabled()) {
            return;
        }

        $shipment = $observer->getShipment();

        //if($shipment->getId()) return $this;
        $vendorOrderId = $shipment->getVendorOrderId();

        $vendorOrder = $this->_objectManager->create('Vnecoms\VendorsSales\Model\Order')->load($vendorOrderId);

        if ($vendorOrder->getId()) {
            if (!$vendorOrder->canInvoice() && !$vendorOrder->canShip()) {
                $vendorOrder->setStatus(Order::STATE_COMPLETE);
            } else {
                $vendorOrder->setStatus(Order::STATE_PROCESSING);
            }
            $vendorOrder->save();
            //send email to vendors
            $this->_shipmentSender->send($shipment, true);
        }

        return $this;
    }
}
