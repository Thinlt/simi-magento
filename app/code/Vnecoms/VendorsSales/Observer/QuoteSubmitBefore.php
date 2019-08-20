<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Observer;

use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitBefore implements ObserverInterface
{
    /**
     * Copy vendor id from quote item to order item
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getQuote();
        $order = $observer->getOrder();
        
        foreach ($order->getAllItems() as $item) {
            $quoteItemId = $item->getQuoteItemId();
            $quoteItem = $quote->getItemById($quoteItemId);
            $item->setVendorId($quoteItem->getVendorId());
        }
        return $this;
    }
}
