<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Controller\Sales\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Vnecoms\VendorsSales\Model\Order\Pdf\Invoice;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Response\Http\FileFactory;
use Vnecoms\Vendors\App\Action\Frontend\Context;
use Magento\Framework\Controller\ResultInterface;
use Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory;
use Vnecoms\VendorsSales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Vnecoms\VendorsSales\Controller\Sales\Order\AbstractMassAction;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Pdfinvoices extends AbstractMassAction
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Invoice
     */
    protected $pdfInvoice;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var InvoiceCollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     * Constructor
     *
     * @param VendorHelper $vendorHelper
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param InvoiceCollectionFactory $invoiceCollectionFactory
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Invoice $pdfInvoice
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Invoice $pdfInvoice
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfInvoice = $pdfInvoice;
        $this->collectionFactory = $collectionFactory;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        parent::__construct($context, $filter);
    }

    /**
     * Print invoices for selected orders
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection)
    {

        $invoicesCollection = $this->invoiceCollectionFactory->create()->setOrderFilter(['in' => $collection->getAllIds()]);
        if (!$invoicesCollection->getSize()) {
            $this->messageManager->addErrorMessage(__('There are no printable documents related to selected orders.'));
            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
        
        return $this->fileFactory->create(
            sprintf('invoices%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
            $this->pdfInvoice->getPdf($invoicesCollection->getItems())->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
