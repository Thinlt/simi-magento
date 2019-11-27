<?php

namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;


class CheckoutCartProductAddAfter implements ObserverInterface {
    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        $this->simiObjectManager = $simiObjectManager;
        $this->scopeConfig = $this->simiObjectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
    }


    public function execute(\Magento\Framework\Event\Observer $observer) {
        $item = $observer->getEvent()->getData('quote_item');
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        $price = $this->_calculateSpecialProductPrice($item);
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->getProduct()->setIsSuperMode(true);
    }

    protected function _calculateSpecialProductPrice($entity) {
        $depositProductId = $this->scopeConfig->getValue('sales/preorder/deposit_product_id');
        if ($entity->getData('product_id') == $depositProductId) {
            //change special product price base on original product
            $block   = $this->simiObjectManager->get('Magento\Checkout\Block\Cart\Item\Renderer');
            $block->setItem($entity);
            $quoteitemOptions = $this->simiObjectManager
                ->get('Simi\Simiconnector\Helper\Checkout')->convertOptionsCart($block->getOptionList());

            foreach ($quoteitemOptions as $quoteitemOption) {
                if ($quoteitemOption['option_title'] == \Simi\Simicustomize\Model\Api\Quoteitems::PRE_ORDER_OPTION_TITLE) {
                    $preOrderProducts = json_decode(base64_decode($quoteitemOption['option_value']), true);
                    if ($preOrderProducts) {
                        $newItemPrice = 0;
                        foreach ($preOrderProducts as $preOrderProduct) {
                            //var_dump($preOrderProduct);die;
                            $newItemPrice =+ 100;
                        }
                        $entity->setCustomPrice($newItemPrice);
                        $entity->setOriginalCustomPrice($newItemPrice);
                        $entity->getProduct()->setIsSuperMode(true);
                        break;
                    }
                }
            }
        }
    }

}