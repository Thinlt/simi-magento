<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Customer\Controller\AbstractAccount;
use Vnecoms\Vendors\App\Action\Frontend\Context;
use Vnecoms\Vendors\Helper\Data as VendorHelper;

abstract class AbstractAction extends AbstractAccount
{
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;
    
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;
    
    /**
     * Constructor
     *
     * @param VendorHelper $vendorHelper
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->_vendorHelper = $context->getVendorHelper();
        $this->_vendorSession = $context->getVendorSession();
        parent::__construct($context);
    }
    
    /**
     * Only actived vendor account can access this page.
     *
     * @see \Magento\Framework\App\Action\Action::dispatch()
     */
    public function dispatch(RequestInterface $request)
    {
        return parent::dispatch($request);
    }
}
