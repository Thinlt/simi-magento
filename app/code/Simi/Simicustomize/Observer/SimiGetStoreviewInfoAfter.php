<?php

namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;
use Simi\VendorMapping\Api\VendorListInterface;

class SimiGetStoreviewInfoAfter implements ObserverInterface {
    public $simiObjectManager;
    public $vendorList;
    
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $config;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        VendorListInterface $vendorList
    ) {
        $this->simiObjectManager = $simiObjectManager;
        $this->vendorList = $vendorList;
        $this->config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $object = $observer->getEvent()->getData('object');
        if ($object->storeviewInfo) {
            //TODO: will be sync with Ocean system in the future
            $object->storeviewInfo['vendor_list'] = $this->vendorList->getVendorList(); //get all vendors
            $object->storeviewInfo['delivery_returns'] = $this->config->getValue('sales/policy/delivery_returns'); //get all vendors
        }
    }
}