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
                if (
                    strpos($routeData['routePath'], '/totals') !== false
                ) {
                    $this->_addDataToTotal($contentArray);
                } else if (
                    strpos($routeData['routePath'], '/shipping-information') !== false &&
                    isset($contentArray['totals']['total_segments'])
                ) {
                    $total = $contentArray['totals'];
                    $this->_addDataToTotal($total);
                    $contentArray['totals'] = $total;
                }
                if (isset($contentArray['totals']['items'])) {
                    $totalData = $contentArray['totals'];
                    $this->_addDataToQuoteItem($totalData);
                    $contentArray['totals'] = $totalData;
                } else
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
        $serviceSupportFee = $this->_getQuote()->getServiceSupportFee();
        if (isset($contentArray['total_segments']) && is_array($contentArray['total_segments'])) {
            $newTotalSegments = array();
            foreach ($contentArray['total_segments'] as $total_segment) {
                $newTotalSegments[] = $total_segment;
                if (isset($total_segment['code']) && $total_segment['code'] == 'subtotal') {
                    if ($depositDiscount) {
                        $newTotalSegments[] = array(
                            'code' => 'preorder_deposit_discount',
                            'title' => __('Pre-order Deposit Discount'),
                            'value' => (float)$depositDiscount,
                        );
                    }
                    if ($serviceSupportFee) {
                        $newTotalSegments[] = array(
                            'code' => 'service_support_fee',
                            'title' => __('Service Support'),
                            'value' => (float)$serviceSupportFee,
                        );
                    }
                }
            }
            $contentArray['total_segments'] = $newTotalSegments;
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
                    $contentArray['items'][$index]['is_buy_service'] = $quoteItem->getData('is_buy_service');
                    //add giftcard info to quote item
                    if ($product->getTypeId() == 'aw_giftcard'){
                        $quoteItemCollection = $this->simiObjectManager->get('Magento\Quote\Model\Quote\Item\Option')->getCollection();
                        $quoteItemCollection->addFieldToFilter('item_id', $quoteItem->getId());
                        $requestData = [];
                        foreach ($quoteItemCollection as $option) {
                            $requestData[$option->getCode()] = $option->getValue();
                        }
                        $contentArray['items'][$index]['giftcard_values'] = $requestData;
                    }
                }
                //modify option values for special products (preorder, trytobuy)
                if (isset($item['options']) && is_string($item['options']) && $optionArray = json_decode($item['options'], true)) {
                    if (is_array($optionArray)) {
                        $systemProductOption = array();
                        $newOptions = false;
                        $extraFieldIndex = false;
                        foreach ($optionArray as $itemOption) {
                            if ($itemOption['label'] === \Simi\Simicustomize\Model\Api\Quoteitems::TRY_TO_BUY_OPTION_TITLE) {
                                $extraFieldIndex = 'simi_trytobuy_option';
                            } else if ($itemOption['label'] === \Simi\Simicustomize\Model\Api\Quoteitems::PRE_ORDER_OPTION_TITLE) {
                                $extraFieldIndex = 'simi_pre_order_option';
                            }
                            if ($extraFieldIndex) {
                                $systemProductOption = json_decode(base64_decode($itemOption['full_view']), true);
                                $newOptions = $systemProductOption;
                                foreach ($newOptions as $newOptionIndex => $newOption ) {
                                    $newOptions[$newOptionIndex] = array(
                                        'label' => $newOption['sku'],
                                        'value' => $newOption['quantity'],
                                        'full_view' => $newOption['name']
                                    );
                                    $productModel = $this->simiObjectManager->create(\Magento\Catalog\Model\Product::class);
                                    $productModel->load($productModel->getIdBySku($newOption['sku']));
                                    $systemProductOption[$newOptionIndex]['product_final_price'] = $productModel->getFinalPrice();
                                    $systemProductOption[$newOptionIndex]['name'] = $productModel->getName();
                                    $systemProductOption[$newOptionIndex]['url_key'] = $productModel->getData('url_key');
                                    $systemProductOption[$newOptionIndex]['vendor_id'] = $productModel->getData('vendor_id');

                                    //to get image + parent data
                                    $product = $this->simiObjectManager
                                        ->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')
                                        ->getParentIdsByChild($productModel->getId());
                                    $imageProductModel = $productModel;
                                    if($product && isset($product[0])){
                                        $media_gallery = $imageProductModel->getMediaGallery();
                                        $parentProductModel = $this->simiObjectManager->create(\Magento\Catalog\Model\Product::class)->load($product[0]);
                                        if ($media_gallery && isset($media_gallery['images']) && is_array($media_gallery['images']) && !count($media_gallery['images'])) {
                                            $imageProductModel = $parentProductModel;
                                        }
                                        $systemProductOption[$newOptionIndex]['name'] = $parentProductModel->getName();
                                        $systemProductOption[$newOptionIndex]['url_key'] = $parentProductModel->getData('url_key');
                                        $systemProductOption[$newOptionIndex]['vendor_id'] = $productModel->getData('vendor_id');
                                    }
                                    $systemProductOption[$newOptionIndex]['image'] =  $this->simiObjectManager
                                        ->create('Simi\Simiconnector\Helper\Products')
                                        ->getImageProduct($imageProductModel);

                                    if (isset($newOption['request']['super_attribute']) && is_array($newOption['request']['super_attribute'])) {
                                        $frontendOption = [];
                                        foreach ($newOption['request']['super_attribute'] as $attributeid=>$attribute) {
                                            $eavModel = $this->simiObjectManager->get('Magento\Catalog\Model\ResourceModel\Eav\Attribute')->load($attributeid);
                                            $frontendOption[] = array(
                                                'label'=>$eavModel->getFrontendLabel(),
                                                'value' => $eavModel->getFrontend()->getValue($productModel)
                                            );
                                        }

                                        $systemProductOption[$newOptionIndex]['frontend_option'] = $frontendOption;
                                    }
                                }
                                break;
                            }
                        }
                        $contentArray['items'][$index][$extraFieldIndex] = json_encode($systemProductOption);

                        if ($newOptions)
                            $contentArray['items'][$index]['options'] = json_encode($newOptions);
                    }
                }
            }
        }
    }

}