<?php
namespace Vnecoms\VendorsConfigApproval\Controller\Adminhtml\Config\Pending;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class SaveChange extends Action
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
        $model = $this->_objectManager->create('Vnecoms\VendorsConfigApproval\Model\Config');

        $model->load($id);
        if (!$model->getUpdateId()) {
            $this->messageManager->addError(__('This change no longer exists.'));
            return;
        }
        
        /* Delete the change after approve*/
        $model->setValue($this->getRequest()->getParam('value'))->save();
        
        $this->messageManager->addSuccess(__('You save the change request.'));
        return;
    }
}
