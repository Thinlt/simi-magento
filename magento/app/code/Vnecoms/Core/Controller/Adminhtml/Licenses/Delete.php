<?php
namespace Vnecoms\Core\Controller\Adminhtml\Licenses;

use Vnecoms\Core\Controller\Adminhtml\Action;

class Delete extends Action
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Vnecoms\Core\Model\Key');

        if ($id) {
            $model->load($id);
            if (!$model->getKeyId()) {
                $this->messageManager->addError(__('This license no longer exists.'));
                $this->_redirect('vnecoms/*');
                return;
            }
            
            try{
                $model->delete();
                $this->messageManager->addSuccess(__("Your license has been deleted."));
            }catch (\Exception $e){
                $this->messageManager->addError($e->getMessage());
            }
        }else{
            $this->messageManager->addError(__("The license id is invalid."));
        }
        /*Everytime we delete the license, just clear the check lincense data*/
        $this->_auth->getAuthStorage()->setData('vnecoms_check_license_data',null);
        $this->_redirect('vnecoms/*');
    }
}
