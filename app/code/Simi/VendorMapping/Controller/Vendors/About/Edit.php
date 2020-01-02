<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\About;

use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Giftcard
 */
class Edit extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Simi_VendorMapping::store_about';

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        // die('123123123 about edit');
        $vendor = $this->_vendorsession->getVendor();

        $this->_initAction();
        $this->setActiveMenu($this->_aclResource);
        $title = $this->_view->getPage()->getConfig()->getTitle();
        $title->prepend(__("About Store"));
        $this->_addBreadcrumb(__("About Store"), __("About Store"));
        $this->_view->renderLayout();
    }
}
