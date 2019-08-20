<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Template;

use Vnecoms\PdfPro\Controller\Adminhtml\Template;

/**
 * Class Delete.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Delete extends Template
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /* @var \VnEcoms\PdfPro\Model\Key $key */
                $temp = $this->templateFactory->create();
                $temp->load($id);
                $temp->delete();
                $this->messageManager->addSuccess(__('The Theme has been deleted.'));
                $resultRedirect->setPath('*/*/');

                return $resultRedirect;
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('*/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a Theme to delete.'));
        // go to grid
        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::theme');
    }
}
