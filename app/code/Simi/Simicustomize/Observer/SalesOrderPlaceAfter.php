<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SalesOrderPlaceAfter implements ObserverInterface
{
    /**
     *  \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function execute(Observer $observer) {
//        $order = $observer->getEvent()->getOrder();
//        $coupon_code = $order->getCouponCode();
//        if ($coupon_code && $coupon_code == 'TRYTOBUY') {
//            //$order->setStatus('try_to_buy');
//        }
//        if ($order->getOrderType() == 'pre_order') {
//            //$order->setStatus('pre_order');
//        }
    }
}
