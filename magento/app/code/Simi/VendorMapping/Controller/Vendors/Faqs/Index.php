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
class Index extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Simi_VendorMapping::store_faqs';

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $vendor = $this->_vendorsession->getVendor();
        if ($vendor && $vendor->getId()) {
            $this->_redirect('*/*/edit/id/'.$vendor->getId());
            return;
        }
        $this->_redirect('*/*/edit');
    }
}
