<?php
namespace Vnecoms\Vendors\Controller\Adminhtml\Group;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class Edit extends Action
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_Vendors::vendors_groups');
    }
    
    
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Vnecoms\Vendors\Model\Group');

        if ($id) {
            $model->load($id);
            if (!$model->getVendorGroupId()) {
                $this->messageManager->addError(__('This group no longer exists.'));
                $this->_redirect('vendors/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        
        $this->_coreRegistry->register('current_group', $model);

        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Seller Groups'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getVendorGroupId() ? $model->getVendorGroupCode() : __('New Seller Group')
        );

        $breadcrumb = $id ? __('Edit Seller Group') : __('New Seller Group');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
}
