<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Customer\Account;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Shopping cart item render block for configurable products.
 */
class Link extends \Magento\Framework\View\Element\Html\Link\Current
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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->creditAccount = $creditAccountFactory->create();
        $this->creditAccount->loadByCustomerId($customerSession->getId());
        
        parent::__construct($context, $defaultPath,$data);
    }
    
    /**
     * Get link label
     * @return string
     */
   public function getLabel(){
       return __("My Credit (%1)","<strong class=\"credit-balance\">".$this->formatBasePrice($this->creditAccount->getCredit(),false)."</strong>");
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
    * Disable escape html for this block.
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
   public function escapeHtml($data, $allowedTags = null){
       return $data;
   }
}
