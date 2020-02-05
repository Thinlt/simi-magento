<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Core\Controller\Adminhtml\Licenses;

use Vnecoms\Core\Controller\Adminhtml\Action;
use Magento\Framework\Exception\LocalizedException;

class Load extends Action
{

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Vnecoms\Core\Model\Key $model */
        $model = $this->_objectManager->create('Vnecoms\Core\Model\Key');
        try {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
                $licenseKey = $model->getLicenseKey();
            }else{
                $licenseKey = trim($this->getRequest()->getParam('license_key'));
                if(!$licenseKey) throw new LocalizedException(__("The license is required."));
                $model->load($licenseKey, 'license_key');
                if($model->getKeyId()) throw new \Exception(__("The license is already exist."));
            }
            
            $licenseInfo = $model->getKeyInfo($licenseKey);
            $model->setData('license_key', $licenseKey);
            $model->setData('license_info', $licenseInfo);
            $model->save();
            
            /*Everytime we load the license, just clear the check lincense data*/
            $this->_auth->getAuthStorage()->setData('vnecoms_check_license_data',null);
            
            if(!$id){
                $this->messageManager->addSuccess(__('You saved the license.'));
                $this->messageManager->addWarning(__("Please add your domains to complete activating the extension"));
            }else{
                $this->messageManager->addSuccess(__('Your license is synced with our server.'));
            }
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);

            $this->_redirect('vnecoms/*/edit', ['id' => $model->getId()]);
            return;
        } catch (LocalizedException $e) {
            /*Unset license data if error*/
            $id = $this->getRequest()->getParam('id');
            if($id){
                $model->load($id);
                $model->setData('license_info', '')->save();
            }
            
            $this->messageManager->addError($e->getMessage());
            if(!$this->getRequest()->getParam('id')){
                $this->_redirect('vnecoms/*/new');
            }else{
                $this->_redirect('vnecoms/*/edit',['id' => $this->getRequest()->getParam('id')]);
            }
            return;
        } catch (\Exception $e) {
            /*Unset license data if error*/
            $id = $this->getRequest()->getParam('id');
            if($id){
                $model->load($id);
                $model->setData('license_info', '')->save();
            }
            $this->messageManager->addError($e->getMessage());
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            if(!$this->getRequest()->getParam('id')){
                $this->_redirect('vnecoms/*/new');
            }else{
                $this->_redirect('vnecoms/*/edit',['id' => $this->getRequest()->getParam('id')]);
            }
            return;
        }
        
        $this->_redirect('vnecoms/*/');
    }
}
