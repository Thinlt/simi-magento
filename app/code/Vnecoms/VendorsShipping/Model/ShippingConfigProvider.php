<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Model;

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
    
    public function __construct(
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory,
        \Vnecoms\VendorsConfig\Helper\Data $vendorConfig,
        CheckoutSession $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        QuoteItemRepository $quoteItemRepository
    ) {
        $this->_vendorFactory = $vendorFactory;
        $this->_vendorConfig = $vendorConfig;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->quoteItemRepository = $quoteItemRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $output = [];
        $quoteId = $this->checkoutSession->getQuote()->getId();
        $quoteItems = $this->quoteItemRepository->getList($quoteId);
        $vendorsList = [];
        foreach ($quoteItems as $index => $quoteItem) {
            if($quoteItem->getIsVirtual()) continue;
            $vendorId = $quoteItem->getVendorId();
            if (!$vendorId) {
                continue;
            }
            $vendor = $this->_vendorFactory->create()->load($vendorId);
            if (!$vendor->getId()) {
                continue;
            }
            
            $vendorsList['vendor_'.$vendorId] = $vendor->getData();
            $title = $this->_vendorConfig->getVendorConfig('general/store_information/name', $vendorId);
            $vendorsList['vendor_'.$vendorId]['shipping_title'] = $title?$title:$vendor->getVendorId();
        }
        $output['vendors_list'] = $vendorsList;
        
        return $output;
    }
}
