<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Template;

use Vnecoms\PdfPro\Controller\Adminhtml\Template;

/**
 * Class MassDelete.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class MassDelete extends Template
{
    /**
     * execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('template');

        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select ID.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $template = $this->templateFactory->create()->load($id);
                    $template->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('An error occurred while deleting record(s).'));
            }
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('*/*/index');

        return $redirectResult;
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::theme');
    }
}
