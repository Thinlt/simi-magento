<?php

namespace Vnecoms\VendorsPdf\Controller\Vendors\Shipment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Pdfshipments extends \Vnecoms\VendorsSales\Controller\Vendors\Shipment\Pdfshipments
{
    /**
     * Print shipments for selected orders
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|\Magento\Backend\Model\View\Result\Redirect
     */
    public function massAction(AbstractCollection $collection)
    {
        if (!$collection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected shipments.'));
    
            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
    
        $shipmentDatas = [];
        $pdfShipment = $this->_objectManager->get('Vnecoms\PdfPro\Model\Order\Shipment');
        foreach ($collection as $shipment) {
            $shipmentDatas[] = $pdfShipment->initShipmentData($shipment);
        }
    
        try {
            $helper = $this->_objectManager->get('Vnecoms\PdfPro\Helper\Data');
            $result = $helper->initPdf($shipmentDatas, 'shipment');
            if ($result['success']) {
                return $this->fileFactory->create(
                    $helper->getFileName('shipments').'.pdf',
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
