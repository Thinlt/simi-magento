<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsConfig\Helper\Data;
use Vnecoms\VendorsSales\Model\Order\Email\Sender\OrderSender;

class ProcessCancelOrder implements ObserverInterface
{
    /**
     * @var \Vnecoms\VendorsSales\Model\OrderFactory
     */
    protected $_vendorOrderFactory;


    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_vendorConfig;

    /**
     * @var OrderSender
     */
    protected $_orderSender;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;

    public function __construct(
        \Vnecoms\VendorsSales\Model\OrderFactory $vendorOrderFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Vnecoms\VendorsSales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        Data $vendorConfig
    ) {
        $this->_vendorOrderFactory = $vendorOrderFactory;
        $this->_eventManager = $eventManager;
        $this->_vendorConfig = $vendorConfig;
        $this->_vendorHelper = $vendorHelper;
        $this->_orderSender = $orderSender;
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

        $order = $observer->getOrder();

        $vendorOrders = $this->_vendorOrderFactory->create()->getCollection()->addFieldToFilter("order_id", $order->getId());
        foreach ($vendorOrders as $vendorOrder) {
            $vendorOrder->cancel();
        }

        return $this;
    }
}
