<?php

namespace Simi\VendorMapping\Observer\System;

use Magento\Framework\Event\ObserverInterface;

class CheckAcl implements ObserverInterface
{
    // /**
    //  * @var EventManager
    //  */
    // private $eventManager;

    // public function __construct(\Magento\Framework\Event\Manager $eventManager)
    // {
    //     $this->eventManager = $eventManager;
    // }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $resource = $observer->getResource();
        $permission = $observer->getPermission();
        // not allow vendor to config shipping method
        if ($resource == 'Vnecoms_VendorsShipping::shipping_methods') {
            $permission->setAllowedFlag(false);
            $permission->push(false);
        }
    }
}
