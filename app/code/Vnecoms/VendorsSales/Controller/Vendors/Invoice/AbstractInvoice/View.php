<?php

namespace Vnecoms\VendorsSales\Controller\Vendors\Invoice\AbstractInvoice;

use Vnecoms\Vendors\App\Action\Context;
use Magento\Framework\Registry;

abstract class View extends \Vnecoms\Vendors\App\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsSales::sales_invoices';
    
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->registry = $context->getCoreRegsitry();
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Invoice information page
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        if ($this->getRequest()->getParam('invoice_id')) {
            $resultForward->setController('order_invoice')
                ->setParams(['come_from' => 'invoice'])
                ->forward('view');
        } else {
            $resultForward->forward('noroute');
        }
        return $resultForward;
    }

    /**
     * @return \Magento\Sales\Model\Order\Invoice|bool
     */
    protected function getInvoice()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        if (!$invoiceId) {
            return false;
        }

        $vendorInvoice = $this->_objectManager->create('\Vnecoms\VendorsSales\Model\Order\Invoice');
        $vendorInvoice->load($invoiceId);
        
        if (!$vendorInvoice->getId()) {
            return false;
        }

        if ($vendorInvoice->getVendorId() != $this->_session->getVendor()->getId()) {
            return false;
        }
        
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $this->_objectManager->create('Magento\Sales\Api\InvoiceRepositoryInterface')->get($vendorInvoice->getInvoiceId());
        
        $this->registry->register('current_invoice', $invoice);
        $this->registry->register('vendor_invoice', $vendorInvoice);
        return $invoice;
    }
}
