<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Adminhtml\Order\Creditmemo\Totals;


class Credit extends \Magento\Framework\View\Element\Template
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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context,$data);
        $this->_coreRegistry = $registry;
    }
    
    /**
     * Retrieve credit memo model instance
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }
    
    
    /**
     * get Credit value that allow admin to refund.
     * @return float
     */
     public function getCreditValue(){
         return $this->getCreditmemo()->getGrandTotal() + abs($this->getCreditmemo()->getCreditAmount());
     }
     
     /**
      * Display the block only for registered customer.
      * @see \Magento\Framework\View\Element\Template::_toHtml()
      */
     protected function _toHtml(){
         if(!$this->getCreditmemo()->getOrder()->getCustomerId()) return '';
         
         return parent::_toHtml();
     }
}
