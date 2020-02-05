<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Controller\Adminhtml\Form\Profile;

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
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_Vendors::vendors_form_profile');
    }
    
    /**
     * @return void
     */
    public function execute()
    {
        $block = $this->_view->getLayout()->createBlock('Vnecoms\Vendors\Block\Adminhtml\Profile\Form')->setTemplate('Vnecoms_Vendors::profile/container_ajax.phtml');
        $this->getResponse()->setBody($block->toHtml());
    }
}
