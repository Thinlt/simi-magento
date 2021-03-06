<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Printinvoices extends \Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro\Order\AbstractMassAction
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
     * @var \Vnecoms\PdfPro\Model\Order\Invoice
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
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * Printinvoices constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param InvoiceCollectionFactory $invoiceCollectionFactory
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param \Vnecoms\PdfPro\Model\Order\Invoice $pdfInvoice
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        DateTime $dateTime,
        FileFactory $fileFactory,
        \Vnecoms\PdfPro\Model\Order\Invoice $pdfInvoice,
        \Vnecoms\PdfPro\Helper\Data $helper
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfInvoice = $pdfInvoice;
        $this->collectionFactory = $collectionFactory;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->helper = $helper;
        parent::__construct($context, $filter);
    }

    /**
     * Print invoices for selected orders.
     *
     * @param AbstractCollection $collection
     *
     * @return ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        $invoiceIds = $collection->getAllIds();
        $orderId = (int) $this->getRequest()->getParam('order_id');
        
        // Print invoices by massaction in order view detail page
        if ($orderId) {
            $invoicesCollection = $this->invoiceCollectionFactory->create()->setOrderFilter(['order_id' => $orderId]);
            if (!empty($invoiceIds)) {
                $invoicesCollection->addFieldToFilter('entity_id', ['in' => $invoiceIds]);
            }
        } else if ($orderId == null) {
            // Print invoices by massaction from order grid
            $invoicesCollection = $this->invoiceCollectionFactory->create()->setOrderFilter(['in' => $collection->getAllIds()]);
        }

        if (!$invoicesCollection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected orders.'));

            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }

        $invoiceDatas = [];
        foreach ($invoicesCollection as $invoice) {
            $invoiceDatas[] = $this->pdfInvoice->initInvoiceData($invoice);
        }

        try {
            $result = $this->helper->initPdf($invoiceDatas, 'invoice');
            if ($result['success']) {
                return $this->fileFactory->create(
                    $this->helper->getFileName('invoices').'.pdf',
                    $result['content'],
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            } else {
                throw new \Exception($result['msg']);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));

            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_print');
    }
}
