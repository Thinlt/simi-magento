<?php

namespace Vnecoms\VendorsPdf\Controller\Vendors\Invoice;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Pdfinvoices extends \Vnecoms\VendorsSales\Controller\Vendors\Invoice\Pdfinvoices
{
    /**
     * Save collection items to pdf invoices
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface
     * @throws \Exception
     */
    public function massAction(AbstractCollection $collection)
    {
        if (!$collection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected invoices.'));
        
            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
        $pdfInvoice = $this->_objectManager->create('Vnecoms\VendorsPdf\Model\Order\Invoice');
        $invoiceDatas = [];
        foreach ($collection as $invoice) {
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
