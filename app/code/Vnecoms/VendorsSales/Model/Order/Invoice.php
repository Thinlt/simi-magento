<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Model\Order;

/**
 * @method \Magento\Customer\Model\Customer getCustomer();
 * @method string getFirstname();
 * @method string getLastname();
 * @method string getMiddlename();
 * @method string getEmail();
 */
class Invoice extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Invoice states
     */
    const STATE_OPEN = 1;
    
    const STATE_PAID = 2;
    
    const STATE_CANCELED = 3;
    
    const ENTITY = 'vendor_invoice';

    
    /**
     * Invoice Object
     * @var \Magento\Sales\Model\Order\Invoice
     */
    protected $_invoice;
    
    /**
     * Vendor order Object
     * @var \Vnecoms\VendorsSales\Model\Order
     */
    protected $_order;
    
    
    /**
     * Vendor Group Object
     * @var \Vnecoms\Vendors\Model\Vendor
     */
    protected $_vendor;
    
    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'vendor_invoice';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'vendor_invoice';
    
    
    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Vnecoms\VendorsSales\Model\ResourceModel\Order\Invoice');
    }
    
    
    /**
     * Get order object
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function getInvoice()
    {
        if (!$this->_invoice) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_invoice = $om->create('Magento\Sales\Model\Order\Invoice');
            $this->_invoice->load($this->getInvoiceId());
        }
        return $this->_invoice;
    }
    
    /**
     * Get vendor order id
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData('vendor_order_id');
    }
    /**
     * Get order
     * @return \Vnecoms\VendorsSales\Model\Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_order = $om->create('Vnecoms\VendorsSales\Model\Order');
            $this->_order->load($this->getVendorOrderId());
        }
        return $this->_order;
    }
    
    /**
     * @return \Vnecoms\VendorsSales\Model\Order
     */
    public function getVendorOrder()
    {
        return $this->getOrder();
    }
    
    /**
     * Get vendor object
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor()
    {
        if (!$this->_vendor) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_vendor = $om->create('Vnecoms\Vendors\Model\Vendor');
            $this->_vendor->load($this->getVendorId());
        }
        return $this->_vendor;
    }
    
    /**
     * Get order state
     * @return string
     */
    public function getState()
    {
        return $this->getData('state');
    }
    
    /**
     * @return \Magento\Sales\Model\Order\Invoice\Item[]
     */
    public function getAllItems()
    {
        if ($this->getData('all_items') == null) {
            $items = [];
            foreach ($this->getInvoice()->getAllItems() as $item) {
                if ($item->getVendorInvoiceId() == $this->getId()) {
                    $items[$item->getId()] = $item;
                }
            }
            
            $this->setData('all_items', $items);
        }
        return $this->getData('all_items');
    }
    
    /**
     * @return array
     */
    public function getAllVisibleItems()
    {
        $items = [];
        foreach ($this->getAllItems() as $item) {
            if (!$item->getParentItemId()) {
                $items[$item->getId()] = $item;
            }
        }
        return $items;
    }
    
    /**
     * Check invoice cancel state
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->getState() == self::STATE_CANCELED;
    }
}
