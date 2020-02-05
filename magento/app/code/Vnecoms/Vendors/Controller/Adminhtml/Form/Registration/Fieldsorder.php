<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Controller\Adminhtml\Form\Registration;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class Fieldsorder extends \Vnecoms\Vendors\Controller\Adminhtml\Form\Profile\Fieldsorder
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Action::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_Vendors::vendors_registration_form');
    }
}
