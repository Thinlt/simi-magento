<?php

namespace Simi\Simicustomize\Controller\Adminhtml\Newcollections;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Delete action
     *
     * @return void
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id             = $this->getRequest()->getParam('newcollections_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $simiobjectManager = $this->_objectManager;
                $model = $simiobjectManager->create('Simi\Simicustomize\Model\Newcollections');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                $simiobjectManager->get('Simi\Simicustomize\Helper\Data')->flushStaticCache();
                // display success message
                $this->messageManager->addSuccess(__('The data has been deleted.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['newcollections_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a data to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
