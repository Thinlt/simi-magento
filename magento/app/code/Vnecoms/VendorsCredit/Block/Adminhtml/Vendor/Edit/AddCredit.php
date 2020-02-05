<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsCredit\Block\Adminhtml\Vendor\Edit;


/**
 * Vendor Notifications block
 */
class AddCredit extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    /**
     * Constructor
     * 
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context,$data);
        $this->_coreRegistry = $registry;
    }
    
    /**
     * Get add credit URL
     * @return string
     */
    public function getAddCreditUrl(){
        return $this->getUrl("storecredit/transaction/save");
    }
    
    
    /**
     * Get customer Id
     * 
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry('current_vendor')->getCustomer()->getId();
    }
}
