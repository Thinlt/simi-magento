<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Account\Create;

use Vnecoms\Vendors\Model\Source\RegisterType;

class Link extends \Magento\Framework\View\Element\Html\Link
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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Vnecoms\Vendors\Helper\Data $vendorHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        \Vnecoms\Vendors\Model\Session $session,
        array $data = []
    ) {
        $this->_vendorHelper = $vendorHelper;
        $this->_vendorSession = $session;
        parent::__construct($context, $data);
    }
    
    /**
     * Is registered vendor
     *
     * @return boolean
     */
    public function getIsRegisteredVendor()
    {
        return $this->_vendorSession->isLoggedIn() && $this->_vendorSession->getVendor()->getId();
    }
    
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getIsRegisteredVendor()?$this->getUrl('vendors'):$this->getUrl('marketplace/seller/login');
    }

    
    /**
     * (non-PHPdoc)
     *
     * @see \Magento\Framework\View\Element\Html\Link::_toHtml()
     */
    protected function _toHtml()
    {
        if (!$this->_vendorHelper->moduleEnabled() ||
            (
                $this->_vendorHelper->getSellerRegisterType() != RegisterType::TYPE_SEPARATED &&
                $this->_vendorSession->getVendor()->getId()
            ) ||
            $this->_vendorSession->getVendor()->getId()
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
