<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 04/02/2017
 * Time: 22:14
 */

namespace Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

use Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

class MassDelete extends Variables
{

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('ids');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select ID.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $variable = $this->customVariablesFactory->create()->load($id);
                    $variable->delete();
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
}
