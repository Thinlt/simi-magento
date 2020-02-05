<?php

namespace Simi\Simicustomize\Plugin;


class QuoteItemToOrderItem
{
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject, callable $proceed, \Magento\Quote\Model\Quote\Item\AbstractItem $item, $additional = []
    ) {
        $orderItem = $proceed($item, $additional);
        if ($orderItem && $item && $item->getData('is_buy_service')) {
            $orderItem->setData('is_buy_service', $item->getData('is_buy_service'));
        }
        return $orderItem;
    }

}