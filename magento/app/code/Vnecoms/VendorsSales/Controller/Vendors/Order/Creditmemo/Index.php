<?php

namespace Vnecoms\VendorsSales\Controller\Vendors\Order\Creditmemo;

class Index extends \Vnecoms\VendorsSales\Controller\Vendors\Creditmemo\AbstractCreditmemo\Index
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsSales::sales_creditmemo';
    
    /**
     * Index page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        return parent::execute();
    }
}
