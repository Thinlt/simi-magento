<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Customer;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Shopping cart item render block for configurable products.
 */
class Credit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    
    /**
     * @var \Vnecoms\Credit\Model\CreditFactory
     */
    protected $creditAccountFactory;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     * @var \Vnecoms\Credit\Model\Processor
     */
    protected $creditProcessor;
    
    /**
     * Preparing global layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Credit'));
    }
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory,
        \Vnecoms\Credit\Model\Processor $creditProcessor,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->priceCurrency = $priceCurrency;
        $this->creditAccountFactory = $creditAccountFactory;
        $this->creditProcessor = $creditProcessor;
        parent::__construct($context,$data);
    }
    
    /**
     * Get credit account
     * @return \Vnecoms\Credit\Model\Credit
     */
    public function getCreditAccount(){
        if(!$this->getData('credit_account')){
            $creditAccount = $this->creditAccountFactory->create();
            $creditAccount->loadByCustomerId($this->customerSession->getCustomerId());
            $this->setData('credit_account',$creditAccount);
        }
        
        return $this->getData('credit_account');
    }
    
    /**
     * Format price
     * @param string $price
     */
    public function formatPrice($price =0){
        $price = $this->priceCurrency->convert($price);
        return $this->priceCurrency->format($price,false);
    }
    
    /**
     * Format base currency
     * @param number $price
     */
    public function formatBasePrice($price=0){
        return $this->_storeManager->getStore()->getBaseCurrency()->formatPrecision($price, 2, [], false);
    }
    
    /**
     * Is using base currency
     * @return boolean
     */
    public function isBaseCurrency(){
        $store = $this->_storeManager->getStore();
        return $store->getBaseCurrencyCode() == $store->getCurrentCurrencyCode();
    }
    /**
     * Get buy credit URL
     * @return string
     */
    public function getBuyCreditUrl(){
        return $this->getUrl('storecredit/buy/');
    }
}
