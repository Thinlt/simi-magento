<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro\Invoice;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;

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
     * @var Invoice
     */
    protected $pdfInvoice;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param DateTime          $dateTime
     * @param FileFactory       $fileFactory
     * @param Invoice           $pdfInvoice
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        DateTime $dateTime,
        FileFactory $fileFactory,
        \Vnecoms\PdfPro\Model\Order\Invoice $pdfInvoice,
        CollectionFactory $collectionFactory,
        \Vnecoms\PdfPro\Helper\Data $helper
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfInvoice = $pdfInvoice;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
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
     * Save collection items to pdf invoices.
     *
     * @param AbstractCollection $collection
     *
     * @return ResponseInterface
     *
     * @throws \Exception
     */
    public function massAction(AbstractCollection $collection)
    {
        if (!$collection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected invoices.'));

            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }

        $invoiceDatas = [];
        foreach ($collection as $invoice) {
            $invoiceDatas[] = $this->pdfInvoice->initInvoiceData($invoice);
        }

        try {
            $result = $this->helper->initPdf($invoiceDatas);
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
     * @return string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ?: 'sales/invoice/';
    }
}
