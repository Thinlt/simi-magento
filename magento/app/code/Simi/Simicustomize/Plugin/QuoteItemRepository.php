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
class QuoteItemRepository
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }
    /**
     * Plugin allow qty = 1 when try to buy update item to cart
     */
//    public function beforeSave(
//        \Magento\Quote\Model\Quote\Item\Repository $repository,
//        $cartItem
//    ){
//        $cartId = $cartItem->getQuoteId();
//        $quote = $this->quoteRepository->getActive($cartId);
//        if ($quote->getCouponCode() == 'TRYTOBUY' && (int)$cartItem->getQty() > 1) {
//            $cartItem->setQty(1);
//        }
//        return [$cartItem];
//    }
}
