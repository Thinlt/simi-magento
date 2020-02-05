<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Controller\Sales\Order;

use Vnecoms\Vendors\Controller\AbstractAction;
use Magento\Framework\View\Result\PageFactory;
use Vnecoms\Vendors\App\Action\Frontend\Context;

class Index extends AbstractAction
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * Constructor
     *
     * @param VendorHelper $vendorHelper
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    
    /**
     * Renders CMS Home page
     *
     * @param string|null $coreRoute
     * @return \Magento\Framework\Controller\Result\Forward
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($coreRoute = null)
    {
        $this->getRequest()->setParam('vendor_id', $this->_vendorSession->getVendor()->getId());
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        
        $resultPage->getConfig()->getTitle()->set(__('Seller Panel'));
        return $resultPage;
    }
}
