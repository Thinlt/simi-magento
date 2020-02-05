<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\Plugin\Order\CreditMemo;

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
        \Magento\Sales\Model\Order\Creditmemo\Item $subject,
        $result
    ) {
        $orderItem = $subject->getOrderItem();
        $orderItem->setCreditRefunded($orderItem->getCreditRefunded() + $subject->getCreditAmount());
        $orderItem->setBaseCreditRefunded($orderItem->getBaseCreditRefunded() + $subject->getBaseCreditAmount());
        return $result;
    }

}
