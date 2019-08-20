<?php
namespace Vnecoms\VendorsProfileNotification\Controller\Adminhtml\Profile\Process;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class Save extends Action
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
        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var \Vnecoms\VendorsProfileNotification\Model\Process $model */
                $model = $this->_objectManager->create('Vnecoms\VendorsProfileNotification\Model\Process');
                
                $data = $this->getRequest()->getParams();
                

                $id = $this->getRequest()->getParam('process_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Exception(__('The profile process is not available.'));
                    }
                }

                $model->addData($data);
        
                $this->_objectManager->get('Magento\Backend\Model\Session')->getProcessData($model->getData());

                $model->save();
        
                $this->messageManager->addSuccess(__('You saved the process.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setGroupData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('vendors/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('vendors/*');
                
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the group data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setGroupData($data);
                $this->_redirect('vendors/*/edit', ['id' => $this->getRequest()->getParam('process_id')]);
                return;
            }
        }
        $this->_redirect('vendors/*/');
    }
}
