<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Plugin;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CartTotalRepository
 * @package Simi\Simicustomize\Plugin
 */
class CartTotalRepository
{
    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $cartSession;

    /**
     * CartTotalRepository constructor.
     * @param \Magento\Quote\Api\Data\TotalsExtensionFactory $extensionFactory
     * @param \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository
     * @param \Magento\SalesRule\Model\Coupon $coupon
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository,
        \Magento\Checkout\Model\Session $cartSession,
        StoreManagerInterface $storeManager
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->storeManager = $storeManager;
        $this->cartSession = $cartSession;
    }

    /**
     * After get cart total. Note: add TRYTOBUY coupon code to quote at here not work after total is collected
     */
    public function afterGet(
        \Magento\Quote\Model\Cart\CartTotalRepository $subject,
        \Magento\Quote\Api\Data\TotalsInterface $result
    ) {
        // if ($this->cartSession->getTryToBuyCode() == 'TRYTOBUY') {
        //     $result->setCouponCode('TRYTOBUY');
        // }
        return $result;
    }
}
