<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Controller\Adminhtml\Reserve;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Simi_Simicustomize::sales_reserve';

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var string[]
     */
    // protected $_publicActions = ['deposit'];

    /**
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
     * Orders grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Simi_Simicustomize::sales_reserve');
        $resultPage->getConfig()->getTitle()->prepend(__('Reserve'));
        $resultPage->addBreadcrumb(__('Sales'), __('Sales'));
        $resultPage->addBreadcrumb(__('Reserve'), __('Reserve'));
        return $resultPage;
    }

    /*
	 * Check permission via ACL resource
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Simi_Simicustomize::sales_reserve');
	}
}
