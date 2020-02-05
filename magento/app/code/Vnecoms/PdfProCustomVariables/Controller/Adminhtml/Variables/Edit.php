<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 22:56
 */

namespace Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

use Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

class Edit extends Variables
{
    /**
     * Rule edit action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('custom_variable_id');
        /** @var \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariables $model */
        $model = $this->customVariablesFactory->create();

        if ($id) {
            $model->load($id);
            $this->coreRegistry->register('pdfprocustomvariables_data', $model);
            if (!$model->getCustomVariableId()) {
                $this->messageManager->addErrorMessage(__('This variable no longer exists.'));
                $this->_redirect('ves_customvariable/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        //$model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        $this->coreRegistry->register('current_variable', $model);

        $this->_initAction();
        $this->_view->getLayout()
            ->getBlock('custom_variables_edit')
            ->setData('action', $this->getUrl('ves_customvariable/*/save'));

        $this->_addBreadcrumb($id ? __('Edit Variable') : __('New Variable'), $id ? __('Edit Variable') : __('New Variable'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getCustomVariableId() ? $model->getName() : __('New Variable')
        );
        $this->_view->renderLayout();
    }
}
