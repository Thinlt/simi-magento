<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Vendor product grid block
 *
 */
namespace Vnecoms\VendorsProduct\Block\Vendors\Product\Grid\Column;

class Price extends \Vnecoms\Vendors\Block\Vendors\Widget\Grid\Column
{
    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }
    
    public function _construct()
    {
        parent::_construct();
        $store = $this->storeManager->getStore(
            (int)$this->getRequest()->getParam('store', 0)
        );
        $currency = $this->localeCurrency->getCurrency($store->getBaseCurrencyCode());

        $this->setData('currency_code', $store->getBaseCurrencyCode());
    }
}
