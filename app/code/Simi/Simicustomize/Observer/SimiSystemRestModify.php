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
        $obj = $observer->getObject();
        $routeData = $observer->getData('routeData');
        $contentArray = $obj->getContentArray();
        if ($routeData && isset($routeData['routePath'])){
            if (
                strpos($routeData['routePath'], 'V1/guest-carts/:cartId') !== false ||
                strpos($routeData['routePath'], 'V1/carts/mine') !== false
            ) {
                $this->_addDataToQuoteItem($contentArray);
            }
        }
        $obj->setContentArray($contentArray);
    }

    //modify quote item
    private function _addDataToQuoteItem(&$contentArray) {
        if (isset($contentArray['items']) && is_array($contentArray['items'])) {
            foreach ($contentArray['items'] as $index => $item) {
                //modify option values for special products (preorder, trytobuy)
                if (isset($item['options']) && is_string($item['options']) && $optionArray = json_decode($item['options'], true)) {
                    if (is_array($optionArray)) {
                        $newOptionvalue = array();
                        foreach ($optionArray as $itemOption) {
                            if ($itemOption['label'] === \Simi\Simicustomize\Model\Api\Quoteitems::PRE_ORDER_OPTION_TITLE) {
                                $newOptionvalue = json_decode(base64_decode($itemOption['full_view']), true);
                            }
                        }
                        $contentArray['items'][$index]['simi_system_product_option'] = json_encode($newOptionvalue);
                    }
                }
            }
        }
    }

}