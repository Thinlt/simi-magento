<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\Plugin\Order\Invoice;

class Item
{
    /**
     * Before prepare product collection handler
     *
     * @param \Magento\Catalog\Model\Layer $subject
     * @param \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRegister(
        \Magento\Sales\Model\Order\Invoice\Item $subject,
        $result
    ) {
        $orderItem = $subject->getOrderItem();
        $orderItem->setCreditInvoiced($orderItem->getCreditInvoiced() + $subject->getCreditAmount());
        $orderItem->setBaseCreditInvoiced($orderItem->getBaseCreditInvoiced() + $subject->getBaseCreditAmount());
        return $result;
    }

}
