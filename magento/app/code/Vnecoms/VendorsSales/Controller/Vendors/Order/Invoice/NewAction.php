<?php

namespace Vnecoms\VendorsSales\Controller\Vendors\Order\Invoice;

use Vnecoms\Vendors\App\AbstractAction;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Vnecoms\VendorsSales\Model\Service\InvoiceService;

class NewAction extends AbstractAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsSales::sales_order_action_invoice';
    
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @param   \Vnecoms\Vendors\App\Action\Context $context,
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param InvoiceService $invoiceService
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context,
        PageFactory $resultPageFactory,
        InvoiceService $invoiceService
    ) {
        $this->registry = $context->getCoreRegsitry();
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->invoiceService = $invoiceService;
    }
	
    /**
     * Redirect to order view page
     *
     * @param int $orderId
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function _redirectToOrder($orderId)
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        return $resultRedirect;
    }

    /**
     * Invoice create page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $vendorOrderId = $this->getRequest()->getParam('order_id');
        $invoiceData = $this->getRequest()->getParam('invoice', []);
        $invoiceItems = isset($invoiceData['items']) ? $invoiceData['items'] : [];

        try {
            /** @var \Vnecoms\VendorsSales\Model\Order $vendorOrder */
            $vendorOrder = $this->_objectManager->create('Vnecoms\VendorsSales\Model\Order')->load($vendorOrderId);
            if (!$vendorOrder->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
            }
            $orderId = $vendorOrder->getOrderId();
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            if (!$order->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
            }

            if (!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The order does not allow an invoice to be created.')
                );
            }
            $invoice = $this->invoiceService->prepareVendorInvoice($vendorOrder, $invoiceItems);

            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t create an invoice without products.')
                );
            }

            $this->registry->register('current_invoice', $invoice);
            $this->registry->register('vendor_order', $vendorOrder);

            $comment = $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true);
            if ($comment) {
                $invoice->setCommentText($comment);
            }

            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $this->setActiveMenu('Vnecoms_VendorsSales::sales_invoices');
            $resultPage->getConfig()->getTitle()->prepend(__('Invoices'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Invoice'));
            return $resultPage;
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            return $this->_redirectToOrder($vendorOrderId);
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception, 'Cannot create an invoice.');
            return $this->_redirectToOrder($vendorOrderId);
        }
    }
}
