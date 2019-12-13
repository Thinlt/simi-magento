<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Simicustompayment\Observer;

use Magento\Framework\Event\ObserverInterface;

class SimiconnectorAddPaymentMethod implements ObserverInterface {

    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
   
        $this->simiObjectManager = $simiObjectManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $object = $observer->getObject();
        $object->addPaymentMethod('payfort_fort_sadad', 3);
        $object->addPaymentMethod('payfort_fort_cc', 1);
        return;
    }

}
