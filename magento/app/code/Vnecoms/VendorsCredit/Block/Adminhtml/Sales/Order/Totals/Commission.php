<?php

namespace Vnecoms\VendorsCredit\Block\Adminhtml\Sales\Order\Totals;


class Commission extends \Magento\Framework\View\Element\Template
{
    protected $source;
    
    /**
     * @var \Magento\Sales\Model\Order|\Vnecoms\VendorsSales\Model\Order
     */
    protected $order;
     /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals()
    {
        /** @var $parent \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals */
        $parent = $this->getParentBlock();
        $this->order = $parent->getOrder();
        $this->source = $parent->getSource();

        $this->initCommission();
        return $this;
    }
    
    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder(){
        return ($this->order instanceof \Magento\Sales\Model\Order)?$this->order:$this->order->getOrder();
    }
    
    /**
     * @return array
     */
    public function getOrderItemsIds(){
        $ids = [];
        foreach($this->order->getAllItems() as $item){
            $ids[] = $item->getId();
        }
        
        return $ids;
    }
    
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function initCommission()
    {
        $orderItemIds = $this->getOrderItemsIds();
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $invoiceItemIds = [];
        $invoiceItemCollection = $om->create('Magento\Sales\Model\ResourceModel\Order\Invoice\Item\Collection');
        $invoiceItemCollection->addFieldToFilter('order_item_id', ['in' => $orderItemIds]);
        $amount = 0;
        foreach($invoiceItemCollection as $item){
            $amount += $item->getCommission();
        }
        $baseCommission = abs($amount);
        
        $commission = $this->getOrder()->getBaseCurrency()->convert($baseCommission, $this->order->getOrderCurrencyCode());
        
        /** @var $parent \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals */
        $parent = $this->getParentBlock();
        
        $totalData = new \Magento\Framework\DataObject([
            'code' => 'commission',
            'field' => 'commission',
            'strong' => false,
            'value' => $commission,
            'base_value' => $baseCommission,
            'label' => __('Marketplace Commission'),
            'area' => 'footer',
        ]);
        $parent->addTotal($totalData, 'discount','last');

        return $this;
    }
}
