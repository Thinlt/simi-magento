<?php

namespace Simi\Simicustomize\Helper;

class SpecialOrder extends \Magento\Framework\App\Helper\AbstractHelper
{

    public $storeManager;
    public $scopeConfig;
    public $simiObjectManager;
    public $inputParamsResolver;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Webapi\Controller\Rest\InputParamsResolver $inputParamsResolver
    ) {
        $this->simiObjectManager = $simiObjectManager;
        $this->scopeConfig = $this->simiObjectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->storeManager = $this->simiObjectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $this->inputParamsResolver = $inputParamsResolver;
        parent::__construct($context);
    }

    public function _getCart()
    {
        return $this->simiObjectManager->get('Magento\Checkout\Model\Cart');
    }

    public function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }


    public function isQuotePreOrder($quote = null)
    {
        if (!$quote) {
            $inputParams = $this->inputParamsResolver->resolve();
            if ($inputParams && is_array($inputParams) && isset($inputParams[0])) {
                $quoteId = $inputParams[0];
                $quoteIdMask = $this->simiObjectManager->get('Magento\Quote\Model\QuoteIdMask');
                if ($quoteIdMask->load($quoteId, 'masked_id')) {
                    if ($quoteIdMask && $maskQuoteId = $quoteIdMask->getData('quote_id'))
                        $quoteId = $maskQuoteId;
                }
                $quoteModel = $this->simiObjectManager->get('Magento\Quote\Model\Quote')->load($quoteId);
                if ($quoteModel->getId() && $quoteModel->getData('is_active')) {
                    $this->simiObjectManager->get('Simi\Simiconnector\Helper\Data')->setQuoteToSession($quoteModel);
                }
            }
            $quote = $this->_getQuote();
        }

        $depositProductId = $this->scopeConfig->getValue('sales/preorder/deposit_product_id');
        $quoteItems = $quote->getItemsCollection();
        foreach($quoteItems as $quoteItem) {
            if ($quoteItem && $quoteItem->getProduct() && $quoteItem->getProduct()->getId() == $depositProductId) {
                return true;
            }
        }
        return false;
    }
}
