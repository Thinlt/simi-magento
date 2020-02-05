<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Holiday;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Simi\Simistorelocator\Controller\Adminhtml\Holiday {

    /**
     * Edit Holiday.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute() {
        $id = $this->getRequest()->getParam(static::PARAM_CRUD_ID);
        /** @var \Simi\Simistorelocator\Model\Holiday $model */
        $model = $this->_createMainModel();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Holiday no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->coreRegistry->register(static::REGISTRY_NAME, $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)->addBreadcrumb(
                $id ? __('Edit Holiday') : __('New Holiday'), $id ? __('Edit Holiday') : __('New Holiday')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Manage Holiday'));
        $resultPage->getConfig()->getTitle()->prepend(
                $model->getId() ?
                        __('Edit Holiday %1', $this->escaper->escapeHtml($model->getHolidayName())) : __('New Holiday')
        );

        return $resultPage;
    }

}
