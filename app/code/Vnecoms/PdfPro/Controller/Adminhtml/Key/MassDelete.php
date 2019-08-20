<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Key;

use Vnecoms\PdfPro\Controller\Adminhtml\Key;

/**
 * Class MassDelete.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class MassDelete extends Key
{
    /**
     * execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        //var_dump($this->getRequest()->getParams());die();
        $ids = $this->getRequest()->getParam('ids');

        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select ID.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $template = $this->keyFactory->create()->load($id);
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
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_apikey');
    }
}
