<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Controller\Adminhtml\Form\Profile;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class Savefieldset extends Action
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_Vendors::vendors_form_profile');
    }
    
    /**
     * Get form type
     * @return string
     */
    public function getForm()
    {
        return \Vnecoms\Vendors\Helper\Data::PROFILE_FORM;
    }
    
    /**
     * Get fieldset block.
     */
    public function getFieldsetBlock()
    {
        return $this->_view->getLayout()->createBlock('Vnecoms\Vendors\Block\Adminhtml\Profile\Form')
        ->setTemplate('Vnecoms_Vendors::profile/container_ajax.phtml');
    }
    /**
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var \Magento\CatalogRule\Model\Rule $model */
                $model = $this->_objectManager->create('Vnecoms\Vendors\Model\Vendor\Fieldset');
        
                $title = $this->getRequest()->getParam('fieldset');
        
                $id = $this->getRequest()->getParam('fieldset_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Exception(__('Wrong fieldset specified.'));
                    }
                }
                $model->setForm($this->getForm());
                $model->setTitle($title);
                
                $model->save();
                $block = $this->getFieldsetBlock();
                $result = ['success'=>true,'form_html'=>$block->toHtml(),'saved_fieldset'=>$model->getData()];
            } catch (\Exception $e) {
                $result = ['success'=>false,'err_msg'=>__('Something went wrong while saving the group data. Please review the error log.')];
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        } else {
            $result = ['success'=>false,'err_msg'=>__('The post data is not valid.')];
        }
        
        $this->getResponse()->setBody(json_encode($result));
    }
}
