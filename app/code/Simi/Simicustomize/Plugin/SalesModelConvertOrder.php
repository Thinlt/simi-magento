<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Plugin;


/**
 * Class CartTotalRepository
 * @package Simi\Simicustomize\Plugin
 */
class SalesModelConvertOrder
{
    /**
     * remove discount amount TRYTOBUY when convert order to invoice
     */
    public function beforeToInvoice(
        \Magento\Sales\Model\Convert\Order $convertOrder,
        $order
    ){
        if ($order->getCouponCode() == 'TRYTOBUY') {
            $order->setData('discount_description', null);
            $order->setData('discount_amount', 0.0000);
            $order->setData('base_discount_amount', 0.0000);
            foreach ($order->getAllItems() as $orderItem) {
                $orderItem->setCouponCode('TRYTOBUY');
            }
        }
        return [$order];
    }

    /**
     * remove discount amount TRYTOBUY when convert order items to invoice items
     */
    public function beforeItemToInvoiceItem(
        \Magento\Sales\Model\Convert\Order $convertOrder,
        $item
    ){
        if ($item->getData('coupon_code') == 'TRYTOBUY') {
            $item->setData('discount_amount', 0.0000);
            $item->setData('discount_percent', 0.0000);
            $item->setData('base_discount_amount', 0.0000);
        }
        return [$item];
    }
}
