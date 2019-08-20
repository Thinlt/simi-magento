<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 22:56
 */

namespace Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

use Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

class Delete extends Variables
{
    /**
     * Delete rule action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('custom_variable_id');
        if ($id) {
            try {
                /** @var \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariables $model */
                $model = $this->customVariablesFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the variable.'));
                $this->_redirect('ves_customvariable/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the variable right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('ves_customvariable/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a variable to delete.'));
        $this->_redirect('ves_customvariable/*/');
    }
}
