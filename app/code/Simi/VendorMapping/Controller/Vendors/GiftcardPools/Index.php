<?php

namespace Simi\VendorMapping\Controller\Vendors\GiftcardPools;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Simi_VendorMapping::giftcard_pools';
    
    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $title = $this->_view->getPage()->getConfig()->getTitle();
        $this->setActiveMenu($this->_aclResource);
        $title->prepend(__("Gift Card Pools"));
        $title->prepend(__("Manage Gift Card Pools"));
        $this->_addBreadcrumb(__("Gift Card Pools"), __("Gift Card Pools"))->_addBreadcrumb(__("Manage Gift Card Pools"), __("Manage Gift Card Pools"));
        $this->_view->renderLayout();
    }
}
