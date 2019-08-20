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
        var_dump($resource);
        die('acl');
    }
}
