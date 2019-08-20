<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Observer\Payment;

use Magento\Framework\Event\ObserverInterface;

class CollectItemsAndAmountsObserver implements ObserverInterface
{
    /**
     * Before register credit memo, add credit to customer credit account.
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $cart = $observer->getCart();
        $baseCreditAmount = $cart->getSalesModel()->getDataUsingMethod('base_credit_amount');
        $cart->addCustomItem(__('Store Credit'), 1, $baseCreditAmount,'store_credit');
    }
}
