<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 22:56
 */

namespace Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

use Magento\Backend\App\Action;
use Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

class Save extends Variables
{


    public function execute()
    {
        if (!$this->getRequest()->getPostValue()) {
            $this->_redirect('ves_customvariable/*/');
        }

        try {
            /** @var $model \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariables */
            $model = $this->customVariablesFactory->create();

            $data = $this->getRequest()->getPostValue();
            $id = $this->getRequest()->getParam('custom_variable_id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);
            $this->_eventManager->dispatch(
                'adminhtml_custom_variables_prepare_save',
                ['request' => $this->getRequest()]
            );
            $this->_session->setPageData($model->getData());
            $model->save();
            $this->messageManager->addSuccessMessage(__('You saved the variable.'));
            $this->_session->setPageData(false);
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('ves_customvariable/*/edit', ['custom_variable_id' => $model->getCustomVariableId()]);
                return;
            }
            $this->_redirect('ves_customvariable/*/');
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int)$this->getRequest()->getParam('custom_variable_id');
            if (!empty($id)) {
                $this->_redirect('ves_customvariable/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('ves_customvariable/*/new');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the variable data. Please review the error log.')
            );
            $this->logger->critical($e);
            $data = !empty($data) ? $data : [];
            $this->_session->setPageData($data);
            $this->_redirect('ves_customvariable/*/edit', ['custom_variable_id' => $this->getRequest()->getParam('custom_variable_id')]);
            return;
        }
    }
}
