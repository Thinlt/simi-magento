<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Controller\AbstractController;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class PrintInvoice.
 *
 * @author Vnecoms team <vnecoms.com>
 */
abstract class PrintInvoice extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface
     */
    protected $orderAuthorization;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Vnecoms\PdfPro\Model\Order
     */
    protected $pdfProOrder;

    /**
     * @var \Vnecoms\PdfPro\Model\Order\Invoice
     */
    protected $pdfProInvoice;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     * PrintInvoice constructor.
     * @param Context $context
     * @param \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface $orderAuthorization
     * @param \Magento\Framework\Registry $registry
     * @param PageFactory $resultPageFactory
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Vnecoms\PdfPro\Model\Order $pdfOrder
     * @param \Vnecoms\PdfPro\Model\Order\Invoice $pdfInvoice
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface $orderAuthorization,
        \Magento\Framework\Registry $registry,
        PageFactory $resultPageFactory,
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Vnecoms\PdfPro\Model\Order $pdfOrder,
        \Vnecoms\PdfPro\Model\Order\Invoice $pdfInvoice,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
    ) {
        $this->orderAuthorization = $orderAuthorization;
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_fileFactory = $fileFactory;
        $this->pdfProOrder = $pdfOrder;
        $this->pdfProInvoice = $pdfInvoice;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Print Invoice Action.
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->helper->getConfig('pdfpro/general/enabled') ||
            !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $invoiceId = (int) $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = $this->_objectManager->create('Magento\Sales\Api\InvoiceRepositoryInterface')->get($invoiceId);
            $order = $invoice->getOrder();
        } else {
            $orderId = (int) $this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        }

        if ($this->orderAuthorization->canView($order)) {
            $this->_coreRegistry->register('current_order', $order);
            if (isset($invoice)) {
                $this->_coreRegistry->register('current_invoice', $invoice);
            }
            $resultRedirect = $this->resultRedirectFactory->create();

            /*for alone invoice print*/
            if (isset($invoice)) {
                $invoiceData = $this->pdfProInvoice->initInvoiceData($invoice);

                try {
                    $result = $this->helper->initPdf(array($invoiceData), 'invoice');
                    if ($result['success']) {
                        return $this->_fileFactory->create(
                            $this->helper->getFileName('invoice', $invoice).'.pdf',
                            $result['content'],
                            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                            'application/pdf'
                        );
                    } else {
                        throw new \Exception($result['msg']);
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $resultRedirect->setPath('sales/order/invoice/order_id/'.$invoice->getOrderId());

                    return $resultRedirect;
                }
            } else {
                //for print all invoices
                if ($orderId) {
                    $invoiceDataCollection = $this->invoiceCollectionFactory->create()->addFieldToFilter('order_id', $orderId);
                    $invoiceData = [];
                    if ($invoiceDataCollection) {
                        foreach ($invoiceDataCollection as $invoice) {
                            $invoiceData[] = $this->pdfProInvoice->initInvoiceData($invoice);
                        }
                    }
                    try {
                        $result = $this->helper->initPdf($invoiceData, 'invoice');
                        if ($result['success']) {
                            return $this->_fileFactory->create(
                                $this->helper->getFileName('invoices').'.pdf',
                                $result['content'],
                                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                                'application/pdf'
                            );
                        } else {
                            throw new \Exception($result['msg']);
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        $resultRedirect->setPath('sales/order/invoice/order_id/'.$orderId);

                        return $resultRedirect;
                    }
                }
            }
        } else {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
                $resultRedirect->setPath('*/*/history');
            } else {
                $resultRedirect->setPath('sales/guest/form');
            }

            return $resultRedirect;
        }
    }
}
