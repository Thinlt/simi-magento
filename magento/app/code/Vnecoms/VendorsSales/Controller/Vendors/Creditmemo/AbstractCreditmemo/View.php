<?php

namespace Vnecoms\VendorsSales\Controller\Vendors\Creditmemo\AbstractCreditmemo;

use Vnecoms\Vendors\App\Action\Context;
use Magento\Framework\Registry;

class View extends \Vnecoms\Vendors\App\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsSales::sales_creditmemo';
    
    /*
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
     * Creditmemo information page
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        if ($this->getRequest()->getParam('creditmemo_id')) {
            $resultForward->setController('order_creditmemo');
            $resultForward->setParams(['come_from' => 'sales_creditmemo']);
            $resultForward->forward('view');
        } else {
            $resultForward->forward('noroute');
        }
        return $resultForward;
    }
}
