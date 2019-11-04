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
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        if ($quote->getQuoteType() == \Simi\Simicustomize\Model\Total\Quote\Preorder::QUOTE_TYPE) {
            $order->setOrderType(\Simi\Simicustomize\Model\Total\Quote\Preorder::QUOTE_TYPE);
            $order->setData('deposit_amount', $quote->getData('deposit_amount'));
            $order->setData('base_deposit_amount', $quote->getData('base_deposit_amount'));
            $order->setData('remaining_amount', $quote->getData('remaining_amount'));
            $order->setData('base_remaining_amount', $quote->getData('base_remaining_amount'));
        }
    }
}
