<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Controller\Vendors\Noroute;

use Vnecoms\Vendors\App\AbstractAction;

class Index extends AbstractAction
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * (non-PHPdoc)
     * @see \Vnecoms\Vendors\App\AbstractAction::_isAllowed()
     */
    protected function _isAllowed(){
        return true;
    }
    
    /**
     * Noroute action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setStatusHeader(404, '1.1', 'Forbidden');
        $resultPage->setHeader('Status', '404 page not found');
        $resultPage->getConfig()->getTitle()->set(__("404 page not found"));
        $resultPage->addHandle('vendors_noroute_index');
        return $resultPage;
    }
}
