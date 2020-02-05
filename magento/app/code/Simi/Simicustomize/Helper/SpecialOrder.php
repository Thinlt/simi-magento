<?php

namespace Simi\Simicustomize\Helper;

class SpecialOrder extends \Magento\Framework\App\Helper\AbstractHelper
{

    public $storeManager;
    public $scopeConfig;
    public $simiObjectManager;
    public $inputParamsResolver;
    public $foundQuoteId;

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

    public function submitQuotFromRestToSession($quoteId = null) {
        $appState = $this->simiObjectManager->get('\Magento\Framework\App\State');
        if($appState->getAreaCode() == 'adminhtml') return;//not allowed admin area

        $inputParams = $this->inputParamsResolver->resolve();
        if ($this->foundQuoteId)
            return;
        if (!$quoteId && $inputParams && is_array($inputParams) && isset($inputParams[0])) {
            $quoteId = $inputParams[0];
            $quoteIdMask = $this->simiObjectManager->get('Magento\Quote\Model\QuoteIdMask');
            if ($quoteIdMask->load($quoteId, 'masked_id')) {
                if ($quoteIdMask && $maskQuoteId = $quoteIdMask->getData('quote_id'))
                    $quoteId = $maskQuoteId;
            }
        }
        if ($quoteId) {
            $quoteModel = $this->simiObjectManager->get('Magento\Quote\Model\Quote')->load($quoteId);
            if ($quoteModel->getId() && $quoteModel->getData('is_active')) {
                $this->foundQuoteId = $quoteModel->getId();
                $this->simiObjectManager->get('Simi\Simiconnector\Helper\Data')->setQuoteToSession($quoteModel);
            }
        }
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
            $this->submitQuotFromRestToSession();
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

    public function getPreOrderProductsFromOrder($orderModel) {
        $depositProductId = $this->scopeConfig->getValue('sales/preorder/deposit_product_id');
        $preOrderProducts = false;
        $orderData = $orderModel->toArray();
        $orderApiModel = $this->simiObjectManager->get('Simi\Simiconnector\Model\Api\Orders');
        $orderData['order_items']     = $orderApiModel->_getProductFromOrderHistoryDetail($orderModel);
        foreach ($orderData['order_items'] as $order_item) {
            if (
                $order_item['product_id'] == $depositProductId &&
                isset($order_item['product_options']['options']) && is_array($order_item['product_options']['options'])
            ) {
                foreach ($order_item['product_options']['options'] as $product_option) {
                    if (isset($product_option['label']) && $product_option['label'] == \Simi\Simicustomize\Model\Api\Quoteitems::PRE_ORDER_OPTION_TITLE) {
                        $preOrderProducts = json_decode(base64_decode($product_option['option_value']), true);
                        break;
                    }
                }
                break;
            }
        }
        return $preOrderProducts;
    }



    public function isQuoteTryToBuy($quote = null)
    {
        if (!$quote) {
            $this->submitQuotFromRestToSession();
            $quote = $this->_getQuote();
        }

        $tryToBuyProductId = $this->scopeConfig->getValue('sales/trytobuy/trytobuy_product_id');
        $quoteItems = $quote->getItemsCollection();
        foreach($quoteItems as $quoteItem) {
            if ($quoteItem && $quoteItem->getProduct() && $quoteItem->getProduct()->getId() == $tryToBuyProductId) {
                return true;
            }
        }
        return false;
    }

}
