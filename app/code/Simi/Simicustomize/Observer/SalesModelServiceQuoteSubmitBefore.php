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

    public function _getQuote()
    {
        return $this->simiObjectManager->get('Magento\Checkout\Model\Cart')->getQuote();
    }

    public function execute(Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        if ($order->getData('quote_id')) {
            $specialOrderHelper = $this->simiObjectManager->get('Simi\Simicustomize\Helper\SpecialOrder');
            $specialOrderHelper->submitQuotFromRestToSession($order->getData('quote_id'));
            $quote = $this->_getQuote();
            $isPreOrder = $specialOrderHelper->isQuotePreOrder();
            if ($isPreOrder) {
                $order->setOrderType(\Simi\Simicustomize\Ui\Component\Sales\Order\Column\OrderType::ORDER_TYPE_PRE_ORDER_WAITING);
            }
            if ($preOrderDepositAmount = $quote->getData('preorder_deposit_discount')) {
                $order->setData('preorder_deposit_discount', $preOrderDepositAmount);
            }
            if ($deposit_order_increment_id = $quote->getData('deposit_order_increment_id')) {
                try {
                    $this->simiObjectManager->create('Magento\Sales\Model\Order')
                        ->loadByIncrementId($deposit_order_increment_id)
                        ->setOrderType(\Simi\Simicustomize\Ui\Component\Sales\Order\Column\OrderType::ORDER_TYPE_PRE_ORDER_PAID)
                        ->save();
                } catch (\Exception $e) {

                }
                $order->setData('deposit_order_increment_id', $deposit_order_increment_id);
            }

            if ($service_support_fee = $quote->getData('service_support_fee')) {
                $order->setData('service_support_fee', $service_support_fee);
            }
        }
    }
}
