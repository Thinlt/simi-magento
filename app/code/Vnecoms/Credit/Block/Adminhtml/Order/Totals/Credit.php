<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Adminhtml\Order\Totals;


class Credit extends \Magento\Framework\View\Element\Template
{
    protected $_source;
    
    protected $_order;
     /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals()
    {
        /** @var $parent \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals */
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        $this->_initCredit();
        return $this;
    }
    
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _initCredit()
    {
        $parent = $this->getParentBlock();
        
        if($this->_source instanceof \Magento\Sales\Model\Order){
            if($refundedCredit = $this->_source->getCreditRefunded()){
                $totalData = new \Magento\Framework\DataObject([
                    'code' => 'refunded_credit',
                    'field' => 'refunded_credit',
                    'strong' => true,
                    'value' => $refundedCredit,
                    'label' => __('Refunded to Store Credit'),
                    'area' => 'footer',
                ]);
                $parent->addTotal($totalData,'refunded');
            }
        }
        
        
        $usedCredit = $this->_source->getCreditAmount();
        $baseUsedCredit = $this->_source->getBaseCreditAmount();
        if(!$usedCredit || abs($usedCredit) == 0 ) return;
        $totalData = new \Magento\Framework\DataObject([
            'code' => 'credit',
            'field' => 'credit',
            'strong' => false,
            'value' => $usedCredit,
            'base_value' => $baseUsedCredit,
            'label' => __('Store Credit'),
        ]);
        $parent->addTotal($totalData, 'discount');
        
        
        return $this;
    }
    
    public function getCreditAmount(){
        return $this->_order->getCreditAmount();
    }
}
