<?php

namespace Vnecoms\VendorsPdf\Controller\Vendors\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Pdfinvoices extends \Vnecoms\VendorsSales\Controller\Vendors\Order\Pdfinvoices
{
    
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
            $this->messageManager->addError(__('There are no printable documents related to selected invoices.'));

            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
        $pdfInvoice = $this->_objectManager->create('Vnecoms\VendorsPdf\Model\Order\Invoice');
        $invoiceDatas = [];
        foreach ($invoicesCollection as $invoice) {
            $invoiceDatas[] = $pdfInvoice->initVendorInvoiceData($invoice);
        }

        try {
            $helper = $this->_objectManager->get('Vnecoms\PdfPro\Helper\Data');
            $result = $helper->initPdf($invoiceDatas);
            if ($result['success']) {
                return $this->fileFactory->create(
                    $helper->getFileName('invoices').'.pdf',
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
}
