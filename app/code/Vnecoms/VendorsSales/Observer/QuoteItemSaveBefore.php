<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Observer;

use Magento\Framework\Event\ObserverInterface;

class QuoteItemSaveBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->_eventManager = $eventManager;
    }
    
    /**
     * Set Vendor Id for Quote Item if it's not exist
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getItem();
        if (!$item->getVendorId()) {
            $vendorId = $item->getProduct()->getVendorId();
            $transport = new \Magento\Framework\DataObject(['vendor_id'=>$vendorId, 'item'=>$item]);
            $this->_eventManager->dispatch('ves_vendors_checkout_init_vendor_id', ['transport'=>$transport]);
            $vendorId = $transport->getVendorId();
            $item->setVendorId($vendorId);
        }

        return $this;
    }
}
