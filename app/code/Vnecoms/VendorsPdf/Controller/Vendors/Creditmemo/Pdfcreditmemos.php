<?php

namespace Vnecoms\VendorsPdf\Controller\Vendors\Creditmemo;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Pdfcreditmemos extends \Vnecoms\VendorsSales\Controller\Vendors\Creditmemo\Pdfcreditmemos
{
    /**
     * Print credit memos for selected orders
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    public function massAction(AbstractCollection $collection)
    {
        if (!$collection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected creditmemos.'));
    
            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
    
        $creditmemoDatas = [];
        $pdfCreditmemo = $this->_objectManager->get('Vnecoms\PdfPro\Model\Order\Creditmemo');
        foreach ($collection as $creditmemo) {
            $creditmemoDatas[] = $pdfCreditmemo->initCreditmemoData($creditmemo);
        }
    
        try {
            $helper = $this->_objectManager->get('Vnecoms\PdfPro\Helper\Data');
            $result = $helper->initPdf($creditmemoDatas, 'creditmemo');
            if ($result['success']) {
                return $this->fileFactory->create(
                    $helper->getFileName('creditmemos').'.pdf',
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
