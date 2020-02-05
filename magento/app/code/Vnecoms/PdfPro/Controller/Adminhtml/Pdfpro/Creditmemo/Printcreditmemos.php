<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro\Creditmemo;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory;

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
     * @var Invoice
     */
    protected $pdfCreditmemo;

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
        \Vnecoms\PdfPro\Model\Order\Creditmemo $pdfCreditmemo,
        CollectionFactory $collectionFactory,
        \Vnecoms\PdfPro\Helper\Data $helper
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfCreditmemo = $pdfCreditmemo;
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
            $this->messageManager->addError(__('There are no printable documents related to selected creditmemos.'));

            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }

        $creditmemoDatas = [];
        foreach ($collection as $creditmemo) {
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
     * @return string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ?: 'sales/creditmemo/';
    }
}
