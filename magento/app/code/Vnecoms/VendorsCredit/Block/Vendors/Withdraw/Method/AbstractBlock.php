<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsCredit\Block\Vendors\Withdraw\Method;

/**
 * Vendor Notifications block
 */
class AbstractBlock extends \Vnecoms\Vendors\Block\Vendors\AbstractBlock
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Vendors\Model\UrlInterface $url,
        \Vnecoms\Vendors\Model\Session $session,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $url, $data);
        $this->_vendorSession = $session;
        $this->_coreRegistry = $coreRegistry;
    }
    
    /**
     * Get Vendor
     *
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor(){
        return $this->_vendorSession->getVendor();
        
    }
    
    /**
     * Can show edit link
     * 
     * @return boolean
     */
    public function canShowEditLink(){
        return $this->_coreRegistry->registry('step') != 'review';
    }
    
    /**
     * Get Edit URL
     * 
     * @return string
     */
    public function getEditUrl(){
        return $this->getUrl('config/index/edit/',['section' => 'withdrawal']);
    }
}
