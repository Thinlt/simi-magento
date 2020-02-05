<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsCredit\Model\CreditProcessor\OrderPayment;
use Vnecoms\VendorsCredit\Model\CreditProcessor\ItemCommission;

class AccountDataProviderObserver implements ObserverInterface
{


    /**
     * Add credit to account collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getCollection();
        $collection->getSelect()->join(['store_credit'=>$collection->getTable('ves_store_credit')], 'store_credit.customer_id=vendor_user.customer_id', ['credit']);
        return $this;
    }
}
