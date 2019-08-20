<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Cart;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Shopping cart item render block for configurable products.
 */
class Credit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    
    /**
     * @var \Vnecoms\Credit\Model\Credit
     */
    protected $creditAccount;
    
    /**
     * Sales quote repository
     *
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     * Credit Helper
     * 
     * @var \Vnecoms\Credit\Helper\Data
     */
    protected $_creditHelper;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Checkout\Model\Cart $cart,
        \Vnecoms\Credit\Helper\Data $creditHelper,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->customerSession = $customerSession;
        $this->_creditHelper = $creditHelper;
        $this->creditAccount = $creditAccountFactory->create();
        if($customerSession->getId()){
            $this->creditAccount->loadByCustomerId($customerSession->getId());
        }
        $this->cart = $cart;
        
        parent::__construct($context,$data);
    }
    
    /**
     * Get Base Credit Amount
     * @return number
     */
    public function getBaseCredit(){
        return $this->creditAccount->getCredit();
    }
    
    /**
     * Get customer credit amount
     * @return number
     */
    public function getCredit(){
        return $this->priceCurrency->convert($this->getBaseCredit());
    }
    
    /**
     * Get Formated Credit
     */
    public function getFormatedCredit(){
        return $this->formatPrice($this->getCredit());
    }
    
    /**
     * Get formated base credit
     * @return number
     */
    public function getFormatedBaseCredit(){
        return $this->formatBasePrice($this->getBaseCredit());
//         $baseCurrency = $this->_storeManager->getStore()->getBaseCurrency();
//         return $baseCurrency->formatPrecision($this->getBaseCredit(),2,[],false);
    }
    
    /**
     * Get used credit.
     * @return float
     */
    public function getUsedCredit(){
        $usedCredit = $this->cart->getQuote()->getCreditAmount() * 1;
        $usedCredit = abs($usedCredit);
        return $usedCredit?$usedCredit:"";
    }
    
    /**
     * Get used base credit
     * @return Ambigous <string, number>
     */
    public function getBaseUsedCredit(){
        $usedCredit = $this->cart->getQuote()->getBaseCreditAmount() * 1;
        $usedCredit = abs($usedCredit);
        return $usedCredit?$usedCredit:"";
    }
    
    /**
     * Format base currency
     * @param number $price
     */
    public function formatPrice($price=0){
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
     * Convert price from base currency to current currency
     * @param number $price
     * @return number
     */
    public function convertPrice($price=0){
        return $this->priceCurrency->convert($price);
    }
    
    /**
     * Get base currency symbol
     */
    public function getBaseCurrencySymbol(){
        return $this->_storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
    }
    
    /**
     * Is using base currency
     * @return boolean
     */
    public function isBaseCurrency(){
        $store = $this->_storeManager->getStore();
        return $store->getCurrentCurrency()->getCurrencyCode() == $store->getBaseCurrency()->getCurrencyCode();
    }
    
    /**
     * Is logged in Customer
     * @return boolean
     */
    public function isLoggedInCustomer(){
        return $this->customerSession->isLoggedIn();
    }
    
    /**
     * Get login URL
     * @return string
     */
    public function getLoginUrl(){
        return $this->getUrl('customer/account/login');
    }
    
    /**
     * Has credit product in shopping cart
     * @return boolean
     */
    public function hasCreditProduct(){
        foreach($this->cart->getQuote()->getAllItems() as $item){
            if($item->getProductType() == \Vnecoms\Credit\Model\Product\Type\Credit::TYPE_CODE)
                return true;
        }
        
        return false;
    }
    
    /**
     * Only selected group can use credit.
     */
    protected function _toHtml(){
        $groupId = $this->customerSession->getCustomerGroupId();
        if(!$this->_creditHelper->canUseCredit($groupId)) return '';
        return parent::_toHtml();
    }
}
