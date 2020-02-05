<?php

namespace Vnecoms\PdfPro\Model;


use Vnecoms\PdfPro\Model\Order\Creditmemo;
use Vnecoms\PdfPro\Model\Order\Shipment;

//require_once BP .'/lib/Vnecoms/mpdf/vendor/autoload.php';

class PrintApi implements \Vnecoms\PdfPro\Api\PrintInterface
{
    protected $helper;

    /**
     * @var \Vnecoms\PdfPro\Model\Order
     */
    protected $pdfProOrder;

    /**
     * @var \Vnecoms\PdfPro\Model\Order\Invoice
     */
    protected $pdfProInvoice;

    /**
     * @var Shipment
     */
    protected $pdfProShipment;

    /**
     * @var Creditmemo
     */
    protected $pdfProCreditmemo;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    public function __construct
    (
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\Order $proOrder,
        \Vnecoms\PdfPro\Model\Order\Invoice $proInvoice,
        \Vnecoms\PdfPro\Model\Order\Shipment $proShipment,
        \Vnecoms\PdfPro\Model\Order\Creditmemo $proCreditmemo,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    )
    {
        $this->helper = $helper;
        $this->pdfProOrder = $proOrder;
        $this->pdfProInvoice = $proInvoice;
        $this->pdfProShipment = $proShipment;
        $this->pdfProCreditmemo = $proCreditmemo;
        $this->_fileFactory = $fileFactory;
    }


    /**
     * @param string $orderId
     * @param string $customerId
     * @return string
     * @throws \Exception
     */
    public function printOrder($orderId, $customerId)
    {
        // TODO: Implement printOrder() method.

        $om = \Magento\Framework\App\ObjectManager::getInstance();

        /**
         * @var \Magento\Sales\Model\Order $order
         */
        $order = $om->create('Magento\Sales\Api\OrderRepositoryInterface')->get($orderId);

        if (!$order->getId()) throw new \Exception(__('Order Not exist'));

        if ($customerId) {
            if ($order->getCustomerId() != $customerId) throw new \Exception(__('Order is not your owned'));
        }

        $orderData = $this->pdfProOrder->initOrderData($order);

        try {
            $result = $this->helper->initPdf(array($orderData), 'order');
            if ($result['success']) {
                return base64_encode($result['content']);
            } else {
                throw new \Exception($result['msg']);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param string $invoiceId
     * @param string $customerId
     * @return string
     * @throws \Exception
     */
    public function printInvoice($invoiceId, $customerId)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();

        $invoice = $om->create('Magento\Sales\Api\InvoiceRepositoryInterface')->get($invoiceId);
        $order = $invoice->getOrder();

        if (!$invoice->getId()) throw new \Exception(__('Invoice Not exist'));

        if ($customerId) {
            if ($invoice->getOrder()->getCustomerId() != $customerId) throw new \Exception(__('Invoice is not your owned'));
        }

        $invoiceData = $this->pdfProInvoice->initInvoiceData($invoice);

        try {
            $result = $this->helper->initPdf(array($invoiceData), 'invoice');
            if ($result['success']) {
                return base64_encode($result['content']);
            } else {
                throw new \Exception($result['msg']);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param string $shipmentId
     * @param string $customerId
     * @return string
     * @throws \Exception
     */
    public function printShipment($shipmentId, $customerId)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();

        $shipment = $om->create('Magento\Sales\Api\ShipmentRepositoryInterface')->get($shipmentId);

        if (!$shipment->getId()) throw new \Exception(__('Shipment Not exist'));

        if ($customerId) {
            if ($shipment->getOrder()->getCustomerId() != $customerId) throw new \Exception(__('Shipment is not your owned'));
        }

        $shipmentData = $this->pdfProShipment->initShipmentData($shipment);

        try {
            $result = $this->helper->initPdf(array($shipmentData), 'shipment');
            if ($result['success']) {
                return base64_encode($result['content']);
            } else {
                throw new \Exception($result['msg']);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param string $creditmemoId
     * @param string $customerId
     * @return string
     * @throws \Exception
     */
    public function printCreditmemo($creditmemoId, $customerId)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();

        $creditmemo = $om->create('Magento\Sales\Api\CreditmemoRepositoryInterface')->get($creditmemoId);

        if (!$creditmemo->getId()) throw new \Exception(__('Creditmemo Not exist'));

        if ($customerId) {
            if ($creditmemo->getOrder()->getCustomerId() != $customerId) throw new \Exception(__('Creditmemo is not your owned'));
        }

        $creditmemoData = $this->pdfProCreditmemo->initCreditmemoData($creditmemo);

        try {
            $result = $this->helper->initPdf(array($creditmemoData), 'creditmemo');
            if ($result['success']) {
                return base64_encode($result['content']);
            } else {
                throw new \Exception($result['msg']);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}