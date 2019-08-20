<?php
namespace Vnecoms\Vendors\Controller\Adminhtml\Index;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class Delete extends Action
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_Vendors::vendors_sellers');
    }
    
    
    /**
     * @return void
     */
    public function execute()
    {
        try {
            /** @var \Magento\CatalogRule\Model\Rule $model */
            $model = $this->_objectManager->create('Vnecoms\Vendors\Model\Vendor');
            $id = $this->getRequest()->getParam('id');
            $model->load($id);
            if ($id != $model->getId()) {
                throw new \Exception(__('Wrong vendor specified.'));
            }

            $model->delete();
    
            $this->messageManager->addSuccess(__('The vendor has been deleted.'));
            $this->_redirect('vendors/index/');
            
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('Something went wrong while deleting the vendor data. Please review the error log.')
            );
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->_redirect('vendors/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        }
        $this->_redirect('vendors/*/');
    }
}
