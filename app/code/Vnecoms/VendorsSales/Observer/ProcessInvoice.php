<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;

class ProcessInvoice implements ObserverInterface
{
    /**
     * @var \Vnecoms\VendorsSales\Model\Order\InvoiceFactory
     */
    protected $_vendorInvoiceFactory;

    /**
     * @var \Vnecoms\VendorsSales\Model\OrderFactory
     */
    protected $_vendorOrderFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;


    /**
     * @var InvoiceSender
     */
    protected $_invoiceSender;


    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;

    /**
     * @var OrderSender
     */
    protected $_orderSender;

    /**
     * Tax module helper
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManage;


    public function __construct(
        \Vnecoms\VendorsSales\Model\OrderFactory $vendorOrderFactory,
        \Vnecoms\VendorsSales\Model\Order\InvoiceFactory $vendorInvoiceFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        \Vnecoms\VendorsSales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Vnecoms\VendorsSales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\Module\Manager $moduleManage
    ) {
        $this->_vendorInvoiceFactory = $vendorInvoiceFactory;
        $this->_vendorOrderFactory = $vendorOrderFactory;
        $this->_eventManager = $eventManager;
        $this->_vendorHelper = $vendorHelper;
        $this->_invoiceSender= $invoiceSender;
        $this->_orderSender = $orderSender;
        $this->_moduleManage = $moduleManage;
    }

    /**
     * Add multiple vendor order row for each vendor.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $log = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Psr\Log\LoggerInterface');
        $log->debug('ProcessInvoice');
        /* Do nothing if the extension is not enabled.*/
        if (!$this->_vendorHelper->moduleEnabled()) {
            return;
        }

        $invoice = $observer->getInvoice();
        if (!$invoice->getId()) {
            return;
        }
        $order = $invoice->getOrder();

        $paymentAction = $order->getPayment()->getMethodInstance()->getConfigPaymentAction();
        if($paymentAction == \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE){
            $order->save();
        }
        /*Check if the vendor invoices is created already*/

        if ($order->getId()) {
            $this->createVendorOrder($order);
        }
        $resourceInvoice = $this->_vendorInvoiceFactory->create()->getResource();

        if ($resourceInvoice->isCreatedVendorInvoice($invoice->getId())) {
            /*Update status of vendor invoices.*/
            $collection = $this->_vendorInvoiceFactory->create()->getCollection();
            $collection->addFieldToFilter('invoice_id', $invoice->getId());

            if ($collection->count()) {
                foreach ($collection as $vendorInvoice) {
                    if ($invoice->getState() == Invoice::STATE_PAID && $vendorInvoice->getState() != Invoice::STATE_PAID) {
                        $vendorOrder = $vendorInvoice->getOrder();

                        $vendorOrder->setTotalDue($vendorOrder->getTotalDue() - $vendorInvoice->getGrandTotal());
                        $vendorOrder->setBaseTotalDue($vendorOrder->getBaseTotalDue() - $vendorInvoice->getBaseGrandTotal());
                        $vendorOrder->setTotalPaid($vendorOrder->getTotalPaid()+$vendorInvoice->getGrandTotal());
                        $vendorOrder->setBaseTotalPaid($vendorOrder->getBaseTotalPaid()+$vendorInvoice->getBaseGrandTotal());

                        $vendorOrder->setShippingInvoiced($vendorOrder->getShippingInvoiced()+ $vendorInvoice->getShippingAmount());
                        $vendorOrder->setBaseShippingInvoiced($vendorOrder->getBaseShippingInvoiced()+$vendorInvoice->getBaseShippingAmount());

                        $vendorOrder->setTotalInvoiced($vendorOrder->getTotalInvoiced()+ $vendorInvoice->getGrandTotal());
                        $vendorOrder->setBaseTotalInvoiced($vendorOrder->getBaseTotalInvoiced()+$vendorInvoice->getBaseGrandTotal());

                        $vendorOrder->setTaxInvoiced($vendorOrder->getTaxInvoiced()+ $vendorInvoice->getTaxAmount());
                        $vendorOrder->setBaseTaxInvoiced($vendorOrder->getBaseTaxInvoiced()+$vendorInvoice->getBaseTaxAmount());

                        $vendorOrder->save();
                    }
                    $vendorInvoice->setState($invoice->getState())->save();
                }
            }
            return;
        }

        /*Create new vendor invoices for this invoice.*/
        $vendorInvoiceItems = [];
        /*Group invoice item by  vendor*/
        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($vendorId = $orderItem->getVendorId()) {
                if (!isset($vendorInvoiceItems[$vendorId])) {
                    $vendorInvoiceItems[$vendorId]=[];
                }
                $vendorInvoiceItems[$vendorId][] = $item;
            }
        }


        $currentTimestamp = (new \DateTime())->getTimestamp();

        foreach ($vendorInvoiceItems as $vendorId => $items) {
            $vendorOrder = $this->_vendorOrderFactory->create();
            $vendorOrderId = $vendorOrder->getResource()->getVendorOrderId($vendorId, $order->getId());
            $vendorOrder->load($vendorOrderId);

            if (!$vendorOrderId || !$vendorOrder->getId()) {
                continue;
            }


            $shippingAmount = 0;
            $baseShippingAmount = 0;
            $shippingInclTaxs = 0;
            $baseShippingInclTaxs = 0;
            $shippingTaxAmount = 0;
            $baseShippingTaxAmount = 0;
            $includeShippingTax = true;
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
                $shippingTaxAmount += $vendorOrder->getShippingTaxAmount();
                $baseShippingTaxAmount += $vendorOrder->getBaseShippingTaxAmount();
            }

            $invoiceData = [
                'vendor_id' => $vendorId,
                'vendor_order_id' => $vendorOrderId,
                'invoice_id' => $invoice->getId(),
                'state' => $invoice->getState(),
                'subtotal' => 0,
                'base_subtotal' => 0,
                'tax_amount' => $shippingTaxAmount,
                'base_tax_amount' => $baseShippingTaxAmount,
                'shipping_tax_amount' => $shippingTaxAmount,
                'base_shipping_tax_amount' => $baseShippingTaxAmount,
                'discount_amount'  => 0,
                'base_discount_amount' => 0,
                'shipping_amount' => $shippingAmount,
                'base_shipping_amount' => $baseShippingAmount,
                'subtotal_incl_tax' => 0,
                'base_subtotal_incl_tax' => 0,
                'total_qty' => sizeof($items),
                'updated_at' => $currentTimestamp,
                'shipping_incl_tax' => $shippingInclTaxs,
                'base_shipping_incl_tax' => $baseShippingInclTaxs,
                'grand_total' => 0,
                'base_grand_total' => 0,
                'base_total_refunded' => 0,
            ];

            foreach ($items as $item) {
                $invoiceData['subtotal'] += $item->getData('row_total');
                $invoiceData['base_subtotal'] += $item->getData('base_row_total');
                $invoiceData['tax_amount'] += $item->getData('tax_amount');
                $invoiceData['base_tax_amount'] += $item->getData('base_tax_amount');
                $invoiceData['discount_amount'] += $item->getData('discount_amount');
                $invoiceData['base_discount_amount'] += $item->getData('base_discount_amount');
                $invoiceData['subtotal_incl_tax'] += $item->getData('row_total_incl_tax');
                $invoiceData['base_subtotal_incl_tax'] += $item->getData('base_row_total_incl_tax');
            }

            $invoiceData['grand_total'] = $invoiceData['subtotal'] +
                $invoiceData['shipping_amount'] +
                $invoiceData['tax_amount'] -
                $invoiceData['discount_amount'];
            $invoiceData['base_grand_total'] = $invoiceData['base_subtotal'] +
                $invoiceData['base_shipping_amount'] +
                $invoiceData['base_tax_amount'] -
                $invoiceData['base_discount_amount'];


            $invoiceDataObj = new \Magento\Framework\DataObject($invoiceData);
            $this->_eventManager->dispatch(
                'ves_vendorssales_process_invoice_before',
                [
                    'invoice_data' => $invoiceDataObj,
                    'vendor_id' => $vendorId,
                    'items' => $items,
                    'invoice' => $invoice
                ]
            );

            $invoiceData = $invoiceDataObj->getData();
            $vendorInvoice = $this->_vendorInvoiceFactory->create();
            
            $vendorInvoice->setData($invoiceData)->setItems($items)->setAllItems($items)->save();
            
            if (!$vendorOrder->canInvoice() && !$vendorOrder->canShip()) {
                $vendorOrder->setStatus(Order::STATE_COMPLETE);
            } else {
                $vendorOrder->setStatus(Order::STATE_PROCESSING);
            }

            if ($invoice->getState() == Invoice::STATE_PAID) {
                $vendorOrder->setTotalDue($vendorOrder->getTotalDue() - $vendorInvoice->getGrandTotal());
                $vendorOrder->setBaseTotalDue($vendorOrder->getBaseTotalDue() - $vendorInvoice->getBaseGrandTotal());
                $vendorOrder->setTotalPaid($vendorOrder->getTotalPaid()+$vendorInvoice->getGrandTotal());
                $vendorOrder->setBaseTotalPaid($vendorOrder->getBaseTotalPaid()+$vendorInvoice->getBaseGrandTotal());

                $vendorOrder->setShippingInvoiced($vendorOrder->getShippingInvoiced()+ $vendorInvoice->getShippingAmount());
                $vendorOrder->setBaseShippingInvoiced($vendorOrder->getBaseShippingInvoiced()+$vendorInvoice->getBaseShippingAmount());

                $vendorOrder->setTotalInvoiced($vendorOrder->getTotalInvoiced()+ $vendorInvoice->getGrandTotal());
                $vendorOrder->setBaseTotalInvoiced($vendorOrder->getBaseTotalInvoiced()+$vendorInvoice->getBaseGrandTotal());

                $vendorOrder->setTaxInvoiced($vendorOrder->getTaxInvoiced()+ $vendorInvoice->getTaxAmount());
                $vendorOrder->setBaseTaxInvoiced($vendorOrder->getBaseTaxInvoiced()+$vendorInvoice->getBaseTaxAmount());
            }

            $vendorOrder->save();

            if ($vendorInvoice->getId()) {
                $this->_invoiceSender->send($vendorInvoice, true);
            }
        }

        return $this;
    }

    public function createVendorOrder($order)
    {
        if (!$order->getId()) {
            return;
        }

        $resourceInvoice = $this->_vendorOrderFactory->create()->getResource();

        if ($resourceInvoice->isCreatedVendorOrder($order->getId())) {
            return;
        }

        $vendorOrderItems = [];

        /*Group order item by  vendor*/
        foreach ($order->getAllItems() as $item) {
            $vendorId = $item->getVendorId();

            if ($vendorId) {
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
                $orderData['subtotal'] += $item->getData('row_total');
                $orderData['base_subtotal'] += $item->getData('base_row_total');
                $orderData['weight'] += $item->getData('row_weight');
                $orderData['tax_amount'] += $item->getData('tax_amount');
                $orderData['base_tax_amount'] += $item->getData('base_tax_amount');
                $orderData['discount_amount'] += $item->getData('discount_amount');
                $orderData['base_discount_amount'] += $item->getData('base_discount_amount');
                $orderData['subtotal_incl_tax'] += $item->getData('row_total_incl_tax');
                $orderData['base_subtotal_incl_tax'] += $item->getData('base_row_total_incl_tax');
                $count++;
            }
			$quote = \Magento\Framework\App\ObjectManager::getInstance()
				->create('Magento\Quote\Model\Quote')->load($order->getQuoteId());
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
    }
}
