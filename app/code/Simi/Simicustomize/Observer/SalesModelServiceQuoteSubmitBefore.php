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
    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->simiObjectManager = $objectManager;
        $this->scopeConfig = $this->simiObjectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
    }

    public function execute(Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $isPreOrder = $this->simiObjectManager->get('Simi\Simicustomize\Helper\SpecialOrder')->isQuotePreOrder();
        if ($isPreOrder) {
            $order->setOrderType(\Simi\Simicustomize\Ui\Component\Sales\Order\Column\OrderType::ORDER_TYPE_PRE_ORDER_WAITING);
        }
    }
}
