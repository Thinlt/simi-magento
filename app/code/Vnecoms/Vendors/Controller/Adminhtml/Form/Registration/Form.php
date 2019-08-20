<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Controller\Adminhtml\Form\Registration;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class Form extends Action
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_Vendors::vendors_registration_form');
    }
    
    /**
     * @return void
     */
    public function execute()
    {
        $block = $this->_view->getLayout()->createBlock('Vnecoms\Vendors\Block\Adminhtml\Registration\Form')->setTemplate('Vnecoms_Vendors::profile/container_ajax.phtml');
        $this->getResponse()->setBody($block->toHtml());
    }
}
