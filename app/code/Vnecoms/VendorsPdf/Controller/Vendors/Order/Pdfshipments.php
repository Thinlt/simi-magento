<?php

namespace Vnecoms\VendorsPdf\Controller\Vendors\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Pdfshipments extends \Vnecoms\VendorsSales\Controller\Vendors\Order\Pdfshipments
{
    /**
     * Print shipments for selected orders
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|\Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $shipmentsCollection = $this->shipmentCollectionFactotory->create()
            ->addFieldToFilter('vendor_order_id', ['in' => $collection->getAllIds()]);
        if (!$shipmentsCollection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected shipments.'));
        
            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
        
        $shipmentDatas = [];
        $pdfShipment = $this->_objectManager->get('Vnecoms\PdfPro\Model\Order\Shipment');
        foreach ($shipmentsCollection as $shipment) {
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
