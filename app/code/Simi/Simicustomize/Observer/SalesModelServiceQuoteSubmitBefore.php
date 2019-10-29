<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SalesModelServiceQuoteSubmitBefore implements ObserverInterface
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
        $isPreorder = false;
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        // check quote has pre-order items
        $items = $quote->getAllItems();
        foreach ($items as $item) {
            $infoRequest = $item->getBuyRequest();
            if ($infoRequest && (int)$infoRequest->getData('pre_order')) {
                $isPreorder = true;
                break;
            }
        }
        if ($isPreorder) {
            $order->setOrderType('pre_order');
            $order->setData('deposit_amount', $quote->getData('deposit_amount'));
            $order->setData('base_deposit_amount', $quote->getData('base_deposit_amount'));
        }
    }
}
