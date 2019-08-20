<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Account;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Link
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Link extends \Magento\Framework\View\Element\Html\Link
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
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @var \Vnecoms\Credit\Helper\Data
     */
    protected $_creditHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory,
        PriceCurrencyInterface $priceCurrency,
        \Vnecoms\Credit\Helper\Data $creditHelper,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->_creditHelper = $creditHelper;
        if($customerSession->isLoggedIn() && ($customerId = $customerSession->getId())){
            $this->creditAccount = $creditAccountFactory->create();
            $this->creditAccount->loadByCustomerId($customerId);
        }
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('storecredit');
    }
    
    /**
     * Get link label
     * @return string
     */
    public function getLabel(){
        return $this->creditAccount?__("My Credit (%1)","<strong class=\"credit-balance\">".$this->formatBasePrice($this->creditAccount->getCredit(),false)."</strong>"):__("My Credit");
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
     * Disable escapehtml for this block
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @see \Magento\Framework\View\Element\AbstractBlock::escapeHtml()
     */
    public function escapeHtml($data, $allowedTags = null){
        return $data;
    }

    public function toHtml(){
        if(!$this->_creditHelper->isDisplayMyCreditOnTopLinks() || !$this->creditAccount) return '';
        
        return '<li class="credit-link"><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
    }
}
