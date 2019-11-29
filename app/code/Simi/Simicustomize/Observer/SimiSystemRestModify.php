<?php

namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;


class SimiSystemRestModify implements ObserverInterface {
    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        $this->simiObjectManager = $simiObjectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $this->simiObjectManager->get('\Simi\Simicustomize\Helper\SpecialOrder')->submitQuotFromRestToSession();
        $obj = $observer->getObject();
        $routeData = $observer->getData('routeData');
        $contentArray = $obj->getContentArray();
        if ($routeData && isset($routeData['routePath'])){
            if (
                strpos($routeData['routePath'], 'V1/guest-carts/:cartId') !== false ||
                strpos($routeData['routePath'], 'V1/carts/mine') !== false
            ) {
                if (strpos($routeData['routePath'], '/totals') !== false) {
                    $this->_addDataToTotal($contentArray);
                }
                $this->_addDataToQuoteItem($contentArray);
            }
        }
        $obj->setContentArray($contentArray);
    }


    public function _getCart()
    {
        return $this->simiObjectManager->get('Magento\Checkout\Model\Cart');
    }

    public function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }


    private function _addDataToTotal(&$contentArray) {
        $depositDiscount = $this->_getQuote()->getPreorderDepositDiscount();
        if ($depositDiscount && isset($contentArray['total_segments']) && is_array($contentArray['total_segments'])) {
            $newTotalSecments = array();
            foreach ($contentArray['total_segments'] as $total_segment) {
                $newTotalSecments[] = $total_segment;
                if (isset($total_segment['code']) && $total_segment['code'] == 'subtotal') {
                    $newTotalSecments[] = array(
                            'code' => 'preorder_deposit_discount',
                            'title' => 'Pre-order Deposit Discount',
                            'value' => $depositDiscount,
                        );
                    }
            }
            $contentArray['total_segments'] = $newTotalSecments;
        }
    }

    private function _addDataToQuoteItem(&$contentArray) {
        if (isset($contentArray['items']) && is_array($contentArray['items'])) {
            foreach ($contentArray['items'] as $index => $item) {
                //add quoteitem product extra data
                $quoteItem = $this->simiObjectManager
                    ->get('Magento\Quote\Model\Quote\Item')->load($item['item_id']);
                if ($quoteItem->getId()) {
                    $product = $this->simiObjectManager
                        ->create('Magento\Catalog\Model\Product')
                        ->load($quoteItem->getData('product_id'));
                    $contentArray['items'][$index]['attribute_values'] = $product->toArray();
                }
                //modify option values for special products (preorder, trytobuy)
                if (isset($item['options']) && is_string($item['options']) && $optionArray = json_decode($item['options'], true)) {
                    if (is_array($optionArray)) {
                        $systemProductOption = array();
                        $newOptions = false;
                        foreach ($optionArray as $itemOption) {
                            if ($itemOption['label'] === \Simi\Simicustomize\Model\Api\Quoteitems::PRE_ORDER_OPTION_TITLE) {
                                $systemProductOption = json_decode(base64_decode($itemOption['full_view']), true);
                                $newOptions = $systemProductOption;
                                foreach ($newOptions as $newOptionIndex => $newOption ) {
                                    $newOptions[$newOptionIndex] = array(
                                        'label' => $newOption['sku'],
                                        'value' => $newOption['quantity'],
                                        'full_view' => $newOption['name']
                                    );
                                }
                            }
                        }
                        $contentArray['items'][$index]['simi_system_product_option'] = json_encode($systemProductOption);
                        if ($newOptions)
                            $contentArray['items'][$index]['options'] = json_encode($newOptions);
                    }
                }
            }
        }
    }

}