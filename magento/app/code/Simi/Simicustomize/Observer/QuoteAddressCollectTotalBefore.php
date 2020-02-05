<?php

namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;


class QuoteAddressCollectTotalBefore implements ObserverInterface {
    public $simiObjectManager;

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $customerGroup;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $cartSession;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        \Magento\Checkout\Model\Session $cartSession
    ) {
        $this->simiObjectManager = $simiObjectManager;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
        $this->customerGroup = $customerGroup;
        $this->cartSession = $cartSession;
    }

    /**
     * This observer will add TRYTOBUY coupon code to the quote and create new one sales rule (coupon code) if it does not existed.
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /** @var $model \Magento\SalesRule\Model\Rule */
//        $rule = $this->simiObjectManager->create(\Magento\SalesRule\Model\Rule::class);
//        $coupon = $this->simiObjectManager->create(\Magento\SalesRule\Model\Coupon::class);
//        $quote = $observer->getEvent()->getQuote();
//        $couponCode = $quote->getCouponCode();
//        if (!$couponCode) {
//            if ($this->cartSession->getTryToBuyCode() == 'TRYTOBUY') {
//                $couponCode = 'TRYTOBUY';
//                $quote->setCouponCode('TRYTOBUY');
//            }
//        }
//        if ($couponCode && $coupon->loadByCode($couponCode) && $couponCode == 'TRYTOBUY') {
//            if (!$coupon->getId()) {
//                $websites = $this->storeManager->getWebsites();
//                $websiteIds = [];
//                foreach($websites as $website){
//                    $websiteIds[] = $website->getId();
//                }
//                $customerGroups = $this->customerGroup->toOptionArray();
//                $customerGroupIds = [];
//                foreach($customerGroups as $group){
//                    $customerGroupIds[] = $group['value'];
//                }
//                $rule->loadPost([
//                    'name' => 'Try to buy',
//                    'uses_per_customer' => 0,
//                    'is_active' => 1,
//                    // conditions_serialized: {"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all"}
//                    // 'actions_serialized': {"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all"}
//                    'stop_rules_processing' => 0,
//                    'is_advanced' => 1,
//                    'sort_order' => 0,
//                    'simple_action' => 'by_percent',
//                    'discount_amount' => 100,
//                    'discount_qty' => 0,
//                    'discount_step' => 0,
//                    'apply_to_shipping' => 0,
//                    'times_used' => 0,
//                    'is_rss' => 0,
//                    'coupon_type' => 2,
//                    'use_auto_generation' => 0,
//                    'uses_per_coupon' => 0,
//                    'simple_free_shipping' => 0,
//                    'code' => 'TRYTOBUY',
//                    'coupon_code' => 'TRYTOBUY',
//                    'website_ids' =>  $websiteIds,
//                    'customer_group_ids' => $customerGroupIds,
//                    'store_labels' =>  ['Try to buy']
//                ]);
//                $rule->save();
//            }
//        }
    }

}