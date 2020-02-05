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
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as MemoCollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Printcreditmemos extends \Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro\Order\AbstractMassAction
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
     * @var \Vnecoms\PdfPro\Model\Order\Creditmemo
     */
    protected $pdfCreditmemo;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var MemoCollectionFactory
     */
    protected $memoCollectionFactory;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * Printcreditmemos constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param MemoCollectionFactory $memoCollectionFactory
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param \Vnecoms\PdfPro\Model\Order\Creditmemo $pdfCreditmemo
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        MemoCollectionFactory $memoCollectionFactory,
        DateTime $dateTime,
        FileFactory $fileFactory,
        \Vnecoms\PdfPro\Model\Order\Creditmemo $pdfCreditmemo,
        \Vnecoms\PdfPro\Helper\Data $helper
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfCreditmemo = $pdfCreditmemo;
        $this->collectionFactory = $collectionFactory;
        $this->memoCollectionFactory = $memoCollectionFactory;
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
        $memoIds = $collection->getAllIds();
        $orderId = (int) $this->getRequest()->getParam('order_id');

        // Print creditmemos by massaction in order view detail page
        if ($orderId) {
            $creditmemosCollection = $this->memoCollectionFactory->create()->setOrderFilter(['order_id' => $orderId]);
            if (!empty($memoIds)) {
                $creditmemosCollection->addFieldToFilter('entity_id', ['in' => $memoIds]);
            }
        } else if ($orderId == null) {
            // Print creditmemos by massaction from order grid
            $creditmemosCollection = $this->memoCollectionFactory->create()->setOrderFilter(['in' => $collection->getAllIds()]);
        }

        if (!$creditmemosCollection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected orders.'));

            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }

        $creditmemoDatas = [];
        foreach ($creditmemosCollection as $creditmemo) {
            $creditmemoDatas[] = $this->pdfCreditmemo->initCreditmemoData($creditmemo);
        }

        try {
            $result = $this->helper->initPdf($creditmemoDatas, 'creditmemo');
            if ($result['success']) {
                return $this->fileFactory->create(
                    $this->helper->getFileName('creditmemos').'.pdf',
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
