<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Schedule;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Simi\Simistorelocator\Controller\Adminhtml\Schedule {

    /**
     * Edit Schedule.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute() {
        $id = $this->getRequest()->getParam(static::PARAM_CRUD_ID);
        /** @var \Simi\Simistorelocator\Model\Schedule $model */
        $model = $this->_createMainModel();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Schedule no longer exists.'));
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
                $id ? __('Edit Schedule') : __('New Schedule'),
                $id ? __('Edit Schedule') : __('New Schedule')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Manage Schedule'));
        $resultPage->getConfig()->getTitle()->prepend(
                $model->getId() ?
                        __('Edit Schedule %1', $this->escaper->escapeHtml($model->getScheduleName())) : __('New Schedule')
        );

        return $resultPage;
    }
}
