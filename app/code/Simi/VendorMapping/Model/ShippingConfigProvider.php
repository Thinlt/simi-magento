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
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $output = [];
        // $vendor = $this->_vendorFactory->create();//->load(0); // load default vendor
        // if (!$vendor->getId()) {
        //     $vendor->setEntityId(0);
        //     $vendor->setVendorId('Default');
        //     $vendor->save();
        // }
        // $vendor->setEntityId(0);
        // $vendor->setVendorId('Default');

        $output['vendors_list']['vendor_default'] = [
            'entity_id' => '0',
            // 'shipping_title' => $vendor->getVendorId()
            'shipping_title' => __('Non Vendor')
        ];
        
        return $output;
    }
}
