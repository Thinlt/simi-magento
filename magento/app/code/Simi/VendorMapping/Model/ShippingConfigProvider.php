<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\VendorMapping\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartItemRepositoryInterface as QuoteItemRepository;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ShippingConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @var \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $_vendorFactory;
    
    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_vendorConfig;
    
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var QuoteItemRepository
     */
    private $quoteItemRepository;

    private $customerFactory;
    
    public function __construct(
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory,
        \Vnecoms\VendorsConfig\Helper\Data $vendorConfig,
        CheckoutSession $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        QuoteItemRepository $quoteItemRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_vendorFactory = $vendorFactory;
        $this->_vendorConfig = $vendorConfig;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->quoteItemRepository = $quoteItemRepository;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Modify shipping config add vendor default
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $output = [];
        $quoteId = $this->checkoutSession->getQuote()->getId();
        $quoteItems = $this->quoteItemRepository->getList($quoteId);
        $isAdminProductExisted = false;
        foreach ($quoteItems as $index => $quoteItem) {
            if($quoteItem->getIsVirtual()) continue;
            $vendorId = $quoteItem->getVendorId();
            if (!$vendorId || $vendorId == 'default') {
                $isAdminProductExisted = true;
            }
        }
        // check if admin's product existed in any quote items
        if ($isAdminProductExisted) {
            $output['vendors_list']['vendor_default'] = [
                'entity_id' => '0',
                'vendor_id' => 'default',
                'shipping_title' => __('Default'),
                // 'shipping_title' => $vendor->getVendorId()
            ];
        }
        
        return $output;
    }
}
