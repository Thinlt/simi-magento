<?php

namespace Vnecoms\VendorsPdf\Plugin\Invoice;


class PrintAction
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
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ){
        $this->helper = $helper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_fileFactory = $fileFactory;
        $this->messageManager = $messageManager;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }
    
    
    /**
     * @param \Vnecoms\VendorsSales\Controller\Vendors\Order\Invoice\PrintAction $subject
     * @param \Closure $proceed
     * @throws \Exception
     * @return \Magento\Framework\App\ResponseInterface|Ambigous <\Magento\Framework\Controller\Result\Redirect, \Magento\Framework\mixed>
     */
    public function aroundExecute(
        \Vnecoms\VendorsSales\Controller\Vendors\Order\Invoice\PrintAction $subject,
        \Closure $proceed
    ) {
        $invoiceId = $subject->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $vendorInvoice = $this->objectManager->create('Vnecoms\VendorsSales\Model\Order\Invoice')->load($invoiceId);

            if ($vendorInvoice->getId()) {
                $invoiceData = $this->objectManager->create('Vnecoms\VendorsPdf\Model\Order\Invoice')->initVendorInvoiceData($vendorInvoice);
                $resultRedirect = $this->resultRedirectFactory->create();
                try {
                    $result = $this->helper->initPdf(array($invoiceData), 'invoice');
                    if ($result['success']) {
                        return $this->_fileFactory->create(
                            $this->helper->getFileName('invoice', $vendorInvoice).'.pdf',
                            $result['content'],
                            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                            'application/pdf'
                        );
                    } else {
                        throw new \Exception($result['msg']);
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $resultRedirect->setPath('sales/order_invoice/view/invoice_id/'.$invoiceId);

                    return $resultRedirect;
                }
            }
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}
