<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsCredit\Block\Vendors\Withdraw;

/**
 * Vendor Notifications block
 */
class MethodList extends \Vnecoms\Vendors\Block\Vendors\AbstractBlock
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     * @var \Vnecoms\VendorsCredit\Helper\Data
     */
    protected $helper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Vnecoms\Vendors\Model\UrlInterface $url
     * @param \Vnecoms\Vendors\Model\Session $session
     * @param \Vnecoms\VendorsCredit\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Vendors\Model\UrlInterface $url,
        \Vnecoms\Vendors\Model\Session $session,
        \Vnecoms\VendorsCredit\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $url, $data);
        $this->_url = $url;
        $this->_vendorSession = $session;
        $this->helper = $helper;
    }
    
    /**
     * Get withdrawal methods
     * 
     * @throws \Exception
     * @return multitype:\Magento\Framework\mixed
     */
    public function getWithdrawalMethods(){
        return $this->helper->getWithdrawalMethods();
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
     * Get Withdrawal URL
     * 
     * @param \Vnecoms\VendorsCredit\Model\Withdrawal\Method\AbstractMethod $method
     * @return string
     */
    public function getWithdrawalUrl(\Vnecoms\VendorsCredit\Model\Withdrawal\Method\AbstractMethod $method){
        return $this->getUrl('credit/withdraw/form',['method'=>$method->getCode()]);
    }
}
