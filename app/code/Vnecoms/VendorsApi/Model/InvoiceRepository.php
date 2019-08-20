<?php

namespace Vnecoms\VendorsApi\Model;

use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Vnecoms\VendorsSales\Model\ResourceModel\Order\Invoice\Grid\CollectionFactory as VendorInvoiceGridCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Vnecoms\VendorsApi\Api\InvoiceRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Vnecoms\VendorsSales\Model\Service\InvoiceService;
use Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterfaceFactory as InvoiceDataInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Vendor repository.
 */
class InvoiceRepository  implements InvoiceRepositoryInterface
{
    /**
     * @var ApiHelper
     */
    protected $helper;

    /**
     * @var VendorInvoiceGridCollectionFactory
     */
    protected $vendorInvoiceGridCollectionFactory;

    /**
     * @var \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceSearchResultInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var InvoiceDataInterfaceFactory
     */
    protected $invoiceDataInterfaceFactory;

    /**
     * InvoiceRepository constructor.
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ApiHelper $helper
     * @param VendorInvoiceGridCollectionFactory $vendorInvoiceGridCollectionFactory
     * @param \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceSearchResultInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     * @param InvoiceService $invoiceService
     * @param \Vnecoms\Vendors\App\Action\Context $context
     * @param InvoiceDataInterfaceFactory $invoiceDataInterfaceFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ApiHelper $helper,
        VendorInvoiceGridCollectionFactory $vendorInvoiceGridCollectionFactory,
        \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceSearchResultInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor = null,
        InvoiceService $invoiceService,
        \Vnecoms\Vendors\App\Action\Context $context,
        InvoiceDataInterfaceFactory $invoiceDataInterfaceFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->vendorInvoiceGridCollectionFactory = $vendorInvoiceGridCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->invoiceService = $invoiceService;
        $this->registry = $context->getCoreRegsitry();
        $this->invoiceDataInterfaceFactory = $invoiceDataInterfaceFactory;
    }

    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceSearchResultInterface
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $collection = $this->vendorInvoiceGridCollectionFactory->create()->addFieldToFilter('vendor_id', $vendorId);

        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $invoices = [];
        /** @var \Vnecoms\VendorsSales\Model\ResourceModel\Order\Invoice\Collection $invoiceModel */
        foreach ($collection as $invoiceModel) {
            $invoiceData = $this->invoiceDataInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $invoiceData,
                $invoiceModel->getData(),
                'Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface'
            );
            $invoices[] = $invoiceData;
        }

        $searchResults->setItems($invoices);
        return $searchResults;
    }

    /**
     * @param int $customerId
     * @param  int $vendorOrderId
     * @param  \Vnecoms\VendorsApi\Api\Data\Sale\ItemQtyInterface[] $items
     * @param  string $comment
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createInvoice(
        $customerId,
        $vendorOrderId,
        $items,
        $comment
    ) {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $data = [];
        $data['comment_text'] = $comment;
        $data['items'] = [];
        foreach ($items as $item){
            $data['items'][$item->getItemId()] = $item->getQty();
        }

        $invoiceItems = isset($data['items']) ? $data['items'] : [];
        /** @var \Vnecoms\VendorsSales\Model\Order $vendorOrder */
        $vendorOrder = $this->_objectManager->create('Vnecoms\VendorsSales\Model\Order')->load($vendorOrderId);

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($vendorOrder->getOrderId());

        if (!$vendorOrder->getId() || !$order->getId()) {
            throw new LocalizedException(__('The order no longer exists.'));
        }

        if($vendorOrder->getVendorId() != $vendor->getId()) throw new LocalizedException(__('The order does not exist'));
        
        if (!$vendorOrder->canInvoice() || !$order->canInvoice()) {
            throw new LocalizedException(
                __('The order does not allow an invoice to be created.')
            );
        }

        $invoice = $this->invoiceService->prepareVendorInvoice($vendorOrder, $invoiceItems);

        if (!$invoice) {
            throw new LocalizedException(__('We can\'t save the invoice right now.'));
        }

        if (!$invoice->getTotalQty()) {
            throw new LocalizedException(
                __('You can\'t create an invoice without products.')
            );
        }
        $this->registry->register('current_invoice', $invoice);
        if (!empty($data['capture_case'])) {
            $invoice->setRequestedCaptureCase($data['capture_case']);
        }

        if (!empty($data['comment_text'])) {
            $invoice->addComment(
                $data['comment_text'],
                isset($data['comment_customer_notify']),
                isset($data['is_visible_on_front'])
            );

            $invoice->setCustomerNote($data['comment_text']);
            $invoice->setCustomerNoteNotify(isset($data['comment_customer_notify']));
        }

        $invoice->register();

        $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
        $invoice->getOrder()->setIsInProcess(true);

        $transactionSave = $this->_objectManager->create(
            'Magento\Framework\DB\Transaction'
        )->addObject(
            $invoice
        )->addObject(
            $invoice->getOrder()
        );
        $shipment = false;
        if (!empty($data['do_shipment']) || (int)$invoice->getOrder()->getForcedShipmentWithInvoice()) {
            $shipment = $this->_prepareShipment($invoice,$vendorOrder);
            if ($shipment) {
                $transactionSave->addObject($shipment);
            }
        }
        $transactionSave->save();

        // send invoice/shipment emails
        if (!empty($data['send_email'])) {
            $this->invoiceSender->send($invoice);
        }

        if ($shipment) {
            if (!empty($data['send_email'])) {
                $this->shipmentSender->send($shipment);
            }
        }

        $invoiceObject = $this->invoiceDataInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $invoiceObject,
            $invoice->getData(),
            'Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface'
        );

        return $invoiceObject;
    }
}

