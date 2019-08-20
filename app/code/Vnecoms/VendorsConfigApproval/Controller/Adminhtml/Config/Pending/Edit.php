<?php
namespace Vnecoms\VendorsConfigApproval\Controller\Adminhtml\Config\Pending;

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
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_VendorsConfigApproval::pending_config');
    }
    
    
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Vnecoms\Vendors\Model\Vendor');

        $model->load($id);
        if (!$model->getVendorId()) {
            $this->messageManager->addError(__('This vendor no longer exists.'));
            $this->_redirect('vendors/*');
            return;
        }
        
        $updateCollection = $this->_objectManager->create('Vnecoms\VendorsConfigApproval\Model\ResourceModel\Config\Collection');
        $updateCollection->addFieldToFilter('vendor_id', $id)
            ->addFieldToFilter('status', \Vnecoms\VendorsConfigApproval\Model\Config::STATUS_PENDING);
        
        if(!$updateCollection->count()){
            $this->_redirect('vendors/*');
            return;
        }
        $this->_coreRegistry->register('current_vendor', $model);
        $this->_coreRegistry->register('vendor', $model);

        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Seller Config Changes'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getVendorId()
        );

        $breadcrumb = __('Manage Seller Config Changes');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
}
