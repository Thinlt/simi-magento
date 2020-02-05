<?php

namespace Vnecoms\VendorsPdf\Controller\Vendors\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultInterface;
use Vnecoms\Vendors\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Pdforders extends \Vnecoms\VendorsSales\Controller\Vendors\Order\AbstractMassAction
{
    /**
     * @var \Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;
    
    /**
     * @param Context $context
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->fileFactory = $fileFactory;
    }
    
    /**
     * Print invoices for selected orders
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        $orderCollection = $this->collectionFactory->create()
            ->addFieldToFilter('vendor_id', $this->_session->getVendor()->getId())
            ->addFieldToFilter('entity_id', ['in' => $collection->getAllIds()]);
        if (!$orderCollection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected orders.'));

            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
        $pdfOrder = $this->_objectManager->create('Vnecoms\VendorsPdf\Model\Order');
        $ordersData = [];
        foreach ($orderCollection as $order) {
            $ordersData[] = $pdfOrder->initVendorOrderData($order);
        }

        try {
            $helper = $this->_objectManager->get('Vnecoms\PdfPro\Helper\Data');
            $result = $helper->initPdf($ordersData, 'order');
            if ($result['success']) {
                return $this->fileFactory->create(
                    $helper->getFileName('orders').'.pdf',
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
