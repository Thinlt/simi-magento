<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Template;

use Vnecoms\PdfPro\Controller\Adminhtml\Template as TemplateController;
use Magento\Framework\Registry;
use Vnecoms\PdfPro\Model\TemplateFactory;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Edit extends TemplateController
{
    /**
     * backend session.
     *
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * constructor.
     *
     * @param Registry        $registry
     * @param TemplateFactory $templateFactory
     * @param BackendSession  $backendSession
     * @param PageFactory     $resultPageFactory
     * @param Context         $context
     * @param RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        // BackendSession $backendSession,
        PageFactory $resultPageFactory,

        Registry $registry,
        TemplateFactory $templateFactory,
        // RedirectFactory $resultRedirectFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        Context $context
    ) {
        parent::__construct($registry, $templateFactory, $resultLayoutFactory, $fileFactory, $context);
        //$this->backendSession = $backendSession;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        $model = $this->initTemplate();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Vnecoms_PdfPro::theme');
        $resultPage->getConfig()->getTitle()->set((__('Themes Management')));
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Theme no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'id' => $model->getId(),
                        '_current' => true,
                    ]
                );

                return $resultRedirect;
            }
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Purchased Themes Manager'), __('Purchased Themes Manager'))
            ->addBreadcrumb(__('New Theme'), __('New Theme'));

        $title = $model->getId() ? $model->getName() : __('New Theme');
        $resultPage->getConfig()->getTitle()->append($title);

        $resultPage->getConfig()->getTitle()->prepend(__('Purchased Theme'));
        if ($model->getId()) {
            $resultPage->getConfig()->getTitle()->prepend($model->getName());
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Theme'));
        }

        /** @var \Magento\Backend\Model\Session $backendSession */
        $backendSession = $this->_objectManager->get('Magento\Backend\Model\Session');
        $data = $backendSession->getData('data', true);
        if (!empty($data)) {
            $model->setData($data);
        }

        return $resultPage;
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::theme');
    }
}
