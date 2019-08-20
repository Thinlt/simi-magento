<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro\Order;

use Vnecoms\PdfPro\Model\Order;
use Vnecoms\PdfPro\Model\Order\Invoice;
use Vnecoms\PdfPro\Model\Order\Shipment;
use Vnecoms\PdfPro\Model\Order\Creditmemo;
use Vnecoms\PdfPro\Helper\Data as Helper;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoCollectionFactory;

/**
 * Class PrintsAction.
 *
 * @author VnEcoms team <vnecoms.com>
 */
class Printdocs extends \Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro\Order\AbstractMassAction
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Helper
     */
    protected $helper;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var Order
     */
    protected $pdfOrder;

    /**
     * @var Invoice
     */
    protected $pdfInvoice;

    /**
     * @var Shipment
     */
    protected $pdfShipment;

    /**
     * @var Creditmemo
     */
    protected $pdfCreditmemo;

    /**
     * @var OrderCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var InvoiceCollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     * @var ShipmentCollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @var CreditmemoCollectionFactory
     */
    protected $creditmemoCollectionFactory;

    /**
     * Printdocs constructor.
     *
     * @param Context                     $context
     * @param Filter                      $filter
     * @param FileFactory                 $fileFactory
     * @param Order                       $pdfOrder
     * @param Invoice                     $pdfInvoice
     * @param Shipment                    $pdfShipment
     * @param Creditmemo                  $pdfCreditmemo
     * @param DateTime                    $dateTime
     * @param ShipmentCollectionFactory   $shipmentCollectionFactory
     * @param InvoiceCollectionFactory    $invoiceCollectionFactory
     * @param CreditmemoCollectionFactory $creditmemoCollectionFactory
     * @param OrderCollectionFactory      $orderCollectionFactory
     * @param Helper                      $pdfHelper
     */
    public function __construct(
        Context $context,
        Filter $filter,
        FileFactory $fileFactory,
        Order $pdfOrder,
        Invoice $pdfInvoice,
        Shipment $pdfShipment,
        Creditmemo $pdfCreditmemo,
        DateTime $dateTime,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        CreditmemoCollectionFactory $creditmemoCollectionFactory,
        OrderCollectionFactory $orderCollectionFactory,
        Helper $pdfHelper
    ) {
        $this->pdfInvoice = $pdfInvoice;
        $this->pdfShipment = $pdfShipment;
        $this->pdfCreditmemo = $pdfCreditmemo;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
        $this->collectionFactory = $orderCollectionFactory;
        $this->helper = $pdfHelper;
        $this->pdfOrder = $pdfOrder;
        parent::__construct($context, $filter);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_print');
    }

    /**
     * @param AbstractCollection $collection
     *
     * @return ResponseInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        $orderIds = $collection->getAllIds();

        $shipments = $this->shipmentCollectionFactory->create()->setOrderFilter(['in' => $orderIds]);
        $invoices = $this->invoiceCollectionFactory->create()->setOrderFilter(['in' => $orderIds]);
        $creditmemos = $this->creditmemoCollectionFactory->create()->setOrderFilter(['in' => $orderIds]);

        $data = array();
        $canPrint = false;

        foreach ($collection as $order) {
            $item = array();
            if ($this->helper->getConfig('pdfpro/general/admin_print_order')) {
                $item['order'][] = $this->pdfOrder->initOrderData($order);
                $canPrint = true;
            }

            if ($invoices->count() > 0) {
                $invoiceDatas = array();
                foreach ($invoices as $invoice) {
                    $invoiceDatas[] = $this->pdfInvoice->initInvoiceData($invoice);
                }
                $item['invoice'] = $invoiceDatas;
                $canPrint = true;
            }

            /*Init shipment data*/
            if ($shipments->count() > 0) {
                $shipmentDatas = array();
                foreach ($shipments as $shipment) {
                    $shipmentDatas[] = $this->pdfShipment->initShipmentData($shipment);
                }
                $item['shipment'] = $shipmentDatas;
                $canPrint = true;
            }

            /*Init credit memo data*/
            if ($creditmemos->count() > 0) {
                $creditmemoDatas = array();
                foreach ($creditmemos as $creditmemo) {
                    $creditmemoDatas[] = $this->pdfCreditmemo->initCreditmemoData($creditmemo);
                }
                $item['creditmemo'] = $creditmemoDatas;
                $canPrint = true;
            }
            $data[] = $item;
        }

        try {
            if (!$canPrint) {
                throw new \Exception(__('There are no printable documents related to selected orders.'));
            }
            $result = $this->helper->initPdf($data, 'all');
            if ($result['success']) {
                return $this->_fileFactory->create(
                    $this->helper->getFileName('all').'.pdf',
                    $result['content'],
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            } else {
                throw new \Exception($result['msg']);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('There are no printable documents related to selected orders.'));

            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
    }
}
