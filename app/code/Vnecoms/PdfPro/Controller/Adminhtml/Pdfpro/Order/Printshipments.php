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
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Printshipments extends \Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro\Order\AbstractMassAction
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
     * @var \Vnecoms\PdfPro\Model\Order\Shipment
     */
    protected $pdfShipment;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    protected $shipmentCollectionFactotory;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * Printshipments constructor.
     *
     * @param Context                              $context
     * @param Filter                               $filter
     * @param CollectionFactory                    $collectionFactory
     * @param DateTime                             $dateTime
     * @param FileFactory                          $fileFactory
     * @param \Vnecoms\PdfPro\Model\Order\Shipment $pdfShipment
     * @param \Vnecoms\PdfPro\Helper\Data          $helper
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DateTime $dateTime,
        FileFactory $fileFactory,
        \Vnecoms\PdfPro\Model\Order\Shipment $pdfShipment,
        \Vnecoms\PdfPro\Helper\Data $helper,
        ShipmentCollectionFactory $shipmentCollectionFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfShipment = $pdfShipment;
        $this->collectionFactory = $collectionFactory;
        $this->shipmentCollectionFactotory = $shipmentCollectionFactory;

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
        $shipmentIds = $collection->getAllIds();
        $orderId = (int) $this->getRequest()->getParam('order_id');
    
        // Print shipments by massaction in order view detail page
        if ($orderId) {
            $shipmentsCollection = $this->shipmentCollectionFactotory->create()->setOrderFilter(['order_id' => $orderId]);
            if (!empty($shipmentIds)) {
                $shipmentsCollection->addFieldToFilter('entity_id', ['in' => $shipmentIds]);
            }
        } else if ($orderId == null) {
            // Print shipments by massaction from order grid
            $shipmentsCollection = $this->shipmentCollectionFactotory->create()->setOrderFilter(['in' => $collection->getAllIds()]);
        }

        if (!$shipmentsCollection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected orders.'));

            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }

        $shipmentDatas = [];
        foreach ($shipmentsCollection as $shipment) {
            $shipmentDatas[] = $this->pdfShipment->initShipmentData($shipment);
        }

        try {
            $result = $this->helper->initPdf($shipmentDatas, 'shipment');
            if ($result['success']) {
                return $this->fileFactory->create(
                    $this->helper->getFileName('shipments').'.pdf',
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
