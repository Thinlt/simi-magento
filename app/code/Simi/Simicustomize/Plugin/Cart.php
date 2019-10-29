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
class Cart
{
    /**
     * Plugin allowed qty = 1 when try to buy adding to cart
     */
    public function beforeAddProduct(
        \Magento\Checkout\Model\Cart $cart,
        $productInfo, $requestInfo
    ){
        if (isset($requestInfo['try_to_buy']) && $requestInfo['try_to_buy']) {
            $requestInfo['qty'] = '1';
            $cart->getQuote()->setCouponCode('TRYTOBUY'); // add coupon code when add try to buy product to the cart
        }
        if (isset($requestInfo['pre_order']) && $requestInfo['pre_order']) {
            $cart->getQuote()->setIsPreorder(true); // set is pre-order - not work
        }
        return [$productInfo, $requestInfo];
    }
}
