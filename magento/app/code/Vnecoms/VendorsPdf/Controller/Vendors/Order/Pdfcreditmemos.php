<?php

namespace Vnecoms\VendorsPdf\Controller\Vendors\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Pdfcreditmemos
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Pdfcreditmemos extends \Vnecoms\VendorsSales\Controller\Vendors\Order\Pdfcreditmemos
{
    /**
     * Print credit memos for selected orders
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        $creditmemoCollection = $this->creditmemoCollectionFactory->create()
            ->addFieldToFilter('vendor_order_id', ['in' => $collection->getAllIds()]);
        if (!$creditmemoCollection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected creditmemos.'));
        
            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
        
        $creditmemoDatas = [];
        $pdfCreditmemo = $this->_objectManager->get('Vnecoms\PdfPro\Model\Order\Creditmemo');
        foreach ($creditmemoCollection as $creditmemo) {
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
