<?php
namespace Vnecoms\VendorsConfigApproval\Controller\Adminhtml\Config\Pending;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class Reject extends Action
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
        $model->setNote($this->getRequest()->getParam('reason'))
            ->setStatus(\Vnecoms\VendorsConfigApproval\Model\Config::STATUS_REJECTED)->save();
        
        $configReader = $this->_objectManager->get('Magento\Config\Model\Config\Structure\Reader');
        $path = explode("/",$model->getPath());
        $sections = $configReader->read('vendors');
        $sections = $sections['config']['system']['sections'];
        $fieldLabel = $path[2];
        if(isset($sections[$path[0]]['children'][$path[1]]['children'][$path[2]])){
            $field = $sections[$path[0]]['children'][$path[1]]['children'][$path[2]];
            $fieldLabel = isset($field['label'])?$field['label']:$path[2];
        }
        $this->_eventManager->dispatch(
            'vnecoms_vendors_push_notification',
            [
                'vendor_id' => $model->getVendorId(),
                'type' => 'config_approval',
                'message' => __('Update of %1 is rejected', '<strong>'.$fieldLabel.'</strong>'),
                'additional_info' => ['path' => $model->getPath()],
            ]
        );
        
        $this->messageManager->addSuccess(__('The change is rejected.'));
        return;
    }
}
