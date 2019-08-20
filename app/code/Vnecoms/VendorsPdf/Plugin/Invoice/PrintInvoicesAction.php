<?php

namespace Vnecoms\VendorsPdf\Plugin\Invoice;


class PrintInvoicesAction
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;
    
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;
    
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;
    
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;
    
    /**
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Ui\Component\MassAction\Filter $filter
    ){
        $this->helper = $helper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_fileFactory = $fileFactory;
        $this->messageManager = $messageManager;
        $this->filter = $filter;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }
    
    
    /**
     * @param \Vnecoms\VendorsSales\Controller\Vendors\Order\Pdfinvoices $subject
     * @param \Closure $proceed
     * @param AbstractCollection $collection
     * @throws \Exception
     * @return \Magento\Framework\App\ResponseInterface|Ambigous <\Magento\Framework\Controller\Result\Redirect, \Magento\Framework\mixed>
     */
    public function aroundMassAction(
        \Vnecoms\VendorsSales\Controller\Vendors\Order\Pdfinvoices $subject,
        \Closure $proceed,
        AbstractCollection $collection
    ) {
        if (!$collection->getSize()) {
            $this->messageManager->addError(__('There are no printable documents related to selected invoices.'));

            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
        $pdfInvoice = $this->objectManager->create('Vnecoms\VendorsPdf\Model\Order\Invoice');
        $invoiceDatas = [];
        foreach ($collection as $invoice) {
            $invoiceDatas[] = $pdfInvoice->initVendorInvoiceData($invoice);
        }

        try {
            $result = $this->helper->initPdf($invoiceDatas);
            if ($result['success']) {
                return $this->fileFactory->create(
                    $this->helper->getFileName('invoices').'.pdf',
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
        return $this->filter->getComponentRefererUrl() ?: 'sales/invoice/';
    }
}
