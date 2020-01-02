<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\Faqs;

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
        die('123123123 about edit');
        $this->_initAction();
        $title = $this->_view->getPage()->getConfig()->getTitle();
        $this->setActiveMenu($this->_aclResource);
        $title->prepend(__("Gift Voucher"));
        $title->prepend(__("Manage Gift Voucher"));
        $this->_addBreadcrumb(__("Gift Voucher"), __("Gift Voucher"))->_addBreadcrumb(__("Manage Gift Voucher"), __("Manage Gift Voucher"));
        $this->_view->renderLayout();
    }
}
