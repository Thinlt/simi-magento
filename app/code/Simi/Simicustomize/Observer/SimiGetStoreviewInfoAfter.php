<?php

namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;
use Simi\VendorMapping\Api\VendorListInterface;

class SimiGetStoreviewInfoAfter implements ObserverInterface {
    public $simiObjectManager;
    public $vendorList;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        VendorListInterface $vendorList
    ) {
        $this->simiObjectManager = $simiObjectManager;
        $this->vendorList = $vendorList;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $object = $observer->getEvent()->getData('object');
        if ($object->storeviewInfo) {
            $object->storeviewInfo['vendor_list'] = $this->vendorList->getVendorList(); //get all vendors
        }
    }
}