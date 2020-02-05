<?php

namespace Simi\Simicustomize\Controller\Adminhtml\Newcollections;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    public $resultPageFactory;

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
     * Simicustomize List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Simi_Simiconnector::simiconnector_manage'
        )->addBreadcrumb(
            __('Newcollections'),
            __('Newcollections')
        )->addBreadcrumb(
            __('Manage Newcollections'),
            __('Manage Newcollections')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Newcollections'));
        return $resultPage;
    }
}
