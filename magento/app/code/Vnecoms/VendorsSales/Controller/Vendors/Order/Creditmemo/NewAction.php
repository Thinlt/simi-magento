<?php

namespace Vnecoms\VendorsSales\Controller\Vendors\Order\Creditmemo;

class NewAction extends \Vnecoms\Vendors\App\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsSales::sales_order_action_creditmemo';
    
    /**
     * @var \Vnecoms\VendorsSales\Controller\Vendors\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param \Vnecoms\Vendors\App\Action\Context $context,
     * @param \Magento\Framework\Registry $registry,
     * @param \Vnecoms\VendorsSales\Controller\Vendors\Order\CreditmemoLoader $creditmemoLoader
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context,
        \Vnecoms\VendorsSales\Controller\Vendors\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->creditmemoLoader = $creditmemoLoader;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $context->getCoreRegsitry();
        parent::__construct($context);
    }

    /**
     * Creditmemo create page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {

        $vendorOrder = $this->_objectManager->create('Vnecoms\VendorsSales\Model\Order')->load($this->getRequest()->getParam('order_id'));
        $this->creditmemoLoader->setOrderId($vendorOrder->getOrderId());
        $this->creditmemoLoader->setVendorOrder($vendorOrder);
        $this->creditmemoLoader->setCreditmemoId($this->getRequest()->getParam('creditmemo_id'));
        $this->creditmemoLoader->setCreditmemo($this->getRequest()->getParam('creditmemo'));
        $this->creditmemoLoader->setInvoiceId($this->getRequest()->getParam('invoice_id'));

        $creditmemo = $this->creditmemoLoader->load();
        if ($creditmemo) {
            if ($comment = $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true)) {
                $creditmemo->setCommentText($comment);
            }

            $this->_coreRegistry->register('vendor_order', $vendorOrder);

            $this->_view->loadLayout();
            $this->_setActiveMenu('Vnecoms_VendorsSales::sales_creditmemo');
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Creditmemo'));
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Creditmemo'));

            if ($creditmemo->getInvoice()) {
                $this->_view->getPage()->getConfig()->getTitle()->prepend(
                    __("New Memo for #%1", $creditmemo->getInvoice()->getIncrementId())
                );
            } else {
                $this->_view->getPage()->getConfig()->getTitle()->prepend(__("New Memo"));
            }

            $this->_view->renderLayout();
        } else {
            $this->_redirect('*/order/view', ['order_id' => $this->getRequest()->getParam('order_id')]);
        }
    }
}
