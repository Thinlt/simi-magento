<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsConfig\Helper\Data;

class ProcessOrder implements ObserverInterface
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
     * Tax module helper
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManage;

    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;


    /**
     * @var \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $_vendorFactory;


    public function __construct(
        \Vnecoms\VendorsSales\Model\OrderFactory $vendorOrderFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Vnecoms\VendorsSales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        \Magento\Framework\Module\Manager $moduleManage,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory,
        Data $vendorConfig
    ) {
        $this->_vendorOrderFactory = $vendorOrderFactory;
        $this->_eventManager = $eventManager;
        $this->_vendorConfig = $vendorConfig;
        $this->_vendorHelper = $vendorHelper;
        $this->_orderSender = $orderSender;
        $this->_moduleManage = $moduleManage;
        $this->_vendorFactory = $vendorFactory;
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
        if (!$order) {
            return;
        }

        $resourceInvoice = $this->_vendorOrderFactory->create()->getResource();

        if ($resourceInvoice->isCreatedVendorOrder($order->getId())) {
            return;
        }
        
        $quote = $observer->getQuote();
        $vendorOrderItems = [];
        
        /*Group order item by  vendor*/
        foreach ($order->getAllItems() as $item) {
            $vendorId = $item->getVendorId();
            $vendor = $this->_vendorFactory->create()->load($vendorId);
            if ($vendorId && $vendor->getId()) {
                if (!isset($vendorOrderItems[$vendorId])) {
                    $vendorOrderItems[$vendorId]=[];
                }
                $vendorOrderItems[$vendorId][] = $item;
            }
        }

        $currentTimestamp = (new \DateTime())->getTimestamp();
        
        foreach ($vendorOrderItems as $vendorId => $items) {
            $vendorOrder = $this->_vendorOrderFactory->create();
            $orderData = [
                'vendor_id' => $vendorId,
                'order_id'  => $order->getId(),
                'status'    => $order->getStatus(),
                'subtotal'  => 0,
                'weight'    => 0,
                'grand_total'   => 0,
                'created_at'    => $currentTimestamp,
                'updated_at'    => $currentTimestamp,
                'tax_amount'    => 0,
                'base_subtotal'     => 0,
                'base_tax_amount'   => 0,
                'discount_amount'   => 0,
                'shipping_amount'   => 0,
                'shipping_incl_tax' => 0,
                'subtotal_incl_tax' => 0,
                'base_subtotal_incl_tax' => 0,
                'shipping_method'   => '',
                'base_discount_amount'  => 0,
                'base_grand_total'      => 0,
                'base_shipping_amount'  => 0,
                'shipping_tax_amount'   => 0,
                'base_shipping_tax_amount'  => 0,
                'base_shipping_incl_tax'    => 0,
                'total_due' => 0,
                'base_total_due' => 0,
            ];
            $count = 0;
            foreach ($items as $item) {
                //Skipping parent items to avoid double calculations
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildrenItems() as $child) {
                        $orderData['subtotal'] += $child->getData('row_total');
                        $orderData['base_subtotal'] += $child->getData('base_row_total');
                        $orderData['weight'] += $child->getData('row_weight');
                        $orderData['tax_amount'] += $child->getData('tax_amount');
                        $orderData['base_tax_amount'] += $child->getData('base_tax_amount');
                        $orderData['discount_amount'] += $child->getData('discount_amount');
                        $orderData['base_discount_amount'] += $child->getData('base_discount_amount');
                        $orderData['subtotal_incl_tax'] += $child->getData('row_total_incl_tax');
                        $orderData['base_subtotal_incl_tax'] += $child->getData('base_row_total_incl_tax');
                        $count += $child->getQtyOrdered();
                    }
                } elseif (!$item->getHasChildren() || !$item->isChildrenCalculated()) {
                    $orderData['subtotal'] += $item->getData('row_total');
                    $orderData['base_subtotal'] += $item->getData('base_row_total');
                    $orderData['weight'] += $item->getData('row_weight');
                    $orderData['tax_amount'] += $item->getData('tax_amount');
                    $orderData['base_tax_amount'] += $item->getData('base_tax_amount');
                    $orderData['discount_amount'] += $item->getData('discount_amount');
                    $orderData['base_discount_amount'] += $item->getData('base_discount_amount');
                    $orderData['subtotal_incl_tax'] += $item->getData('row_total_incl_tax');
                    $orderData['base_subtotal_incl_tax'] += $item->getData('base_row_total_incl_tax');
                    $count += $item->getQtyOrdered();
                }
            }
            
            $orderDataObj = new \Magento\Framework\DataObject($orderData);
            $this->_eventManager->dispatch(
                'ves_vendorssales_process_order_before',
                [
                    'order_data' => $orderDataObj,
                    'vendor_id' => $vendorId,
                    'items' => $items,
                    'order' => $order,
                    'quote' => $quote,
                ]
            );
            $orderData = $orderDataObj->getData();

            $orderData['total_qty_ordered'] = $count;

            if ($this->_moduleManage->isEnabled("Vnecoms_VendorsTax")) {
                $orderData['grand_total'] = $orderData['subtotal_incl_tax'] +
                    $orderData['shipping_incl_tax'] -
                    $orderData['discount_amount'];

                $orderData['base_grand_total'] = $orderData['base_subtotal_incl_tax'] +
                    $orderData['base_shipping_incl_tax'] -
                    $orderData['base_discount_amount'];
            } else {
                $orderData['grand_total'] = $orderData['subtotal'] +
                    $orderData['shipping_amount'] +
                    $orderData['tax_amount'] -
                    $orderData['discount_amount'];

                $orderData['base_grand_total'] = $orderData['base_subtotal'] +
                    $orderData['base_shipping_amount'] +
                    $orderData['base_tax_amount'] -
                    $orderData['base_discount_amount'];
            }

            $orderDataAfterObj = new \Magento\Framework\DataObject($orderData);
            $this->_eventManager->dispatch(
                'ves_vendorssales_process_order_after',
                [
                    'order_data' => $orderDataAfterObj,
                    'vendor_id' => $vendorId,
                    'items' => $items,
                    'order' => $order,
                    'quote' => $quote,
                ]
            );
            $orderData = $orderDataAfterObj->getData();

            $orderData['total_due'] = $orderData['grand_total'];
            $orderData['base_total_due'] = $orderData['base_grand_total'];

            $vendorOrder->setData($orderData)->setItems($items)->save();

            $this->_eventManager->dispatch(
                'vnecoms_vendors_push_notification',
                [
                    'vendor_id' => $vendorId,
                    'type' => 'sales',
                    'message' => __('New order #%1 is placed', '<strong>'.$order->getIncrementId().'</strong>'),
                    'additional_info' => ['id' => $vendorOrder->getId()],
                ]
            );
            
            if ($vendorOrder->getId()) {
                $this->_orderSender->send($vendorOrder, true);
            }
        }
        
        return $this;
    }
}
