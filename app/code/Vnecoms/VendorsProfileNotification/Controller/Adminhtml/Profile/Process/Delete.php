<?php
namespace Vnecoms\VendorsProfileNotification\Controller\Adminhtml\Profile\Process;

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
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_VendorsProfileNotification::manage_process');
    }
    
    
    /**
     * @return void
     */
    public function execute()
    {
        try {
            /** @var \Vnecoms\VendorsProfileNotification\Model\Process $model */
            $model = $this->_objectManager->create('Vnecoms\VendorsProfileNotification\Model\Process');
            $id = $this->getRequest()->getParam('id');
            $model->load($id);
            if ($id != $model->getId()) {
                throw new \Exception(__('Wrong process specified.'));
            }

            $model->delete();
    
            $this->messageManager->addSuccess(__('The process has been deleted.'));
            $this->_redirect('vendors/*/');
            
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('Something went wrong while deleting the group data. Please review the error log.')
            );
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->_redirect('vendors/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        }
        $this->_redirect('vendors/*/');
    }
}
