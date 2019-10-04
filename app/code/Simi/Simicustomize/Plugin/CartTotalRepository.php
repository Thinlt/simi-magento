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
    private $storeManager;

    /**
     * CartTotalRepository constructor.
     * @param \Magento\Quote\Api\Data\TotalsExtensionFactory $extensionFactory
     * @param \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository
     * @param \Magento\SalesRule\Model\Coupon $coupon
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Quote\Model\Cart\CartTotalRepository $subject
     * @param \Magento\Quote\Api\Data\TotalsInterface $result
     * @return \Magento\Quote\Api\Data\TotalsInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        \Magento\Quote\Model\Cart\CartTotalRepository $subject,
        \Magento\Quote\Api\Data\TotalsInterface $result
    ) {
        // $result->setGrandTotal(0);
        return $result;
    }
}
