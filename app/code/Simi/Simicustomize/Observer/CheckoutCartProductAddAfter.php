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
        $this->_calculateSpecialProductPrice($item);
    }

    public function _getCart()
    {
        return $this->simiObjectManager->get('Magento\Checkout\Model\Cart');
    }

    public function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }


    protected function _calculateSpecialProductPrice($entity) {
        $depositProductId = $this->scopeConfig->getValue('sales/preorder/deposit_product_id');
        $depositPercent = $this->scopeConfig->getValue('sales/preorder/deposit_amount');
        if ($entity->getData('product_id') == $depositProductId) {
            //change special product price base on original product
            $registry = $this->simiObjectManager->get('\Magento\Framework\Registry');
            $optionString = $registry->registry('simi_pre_order_option');
            if ($optionString) {
                $preOrderProducts = json_decode(base64_decode($optionString), true);
                if ($preOrderProducts) {
                    $newItemPrice = 0;
                    foreach ($preOrderProducts as $preOrderProduct) {
                        $productModel = $this->simiObjectManager->create(\Magento\Catalog\Model\Product::class);
                        $productModel->load($productModel->getIdBySku($preOrderProduct['sku']));
                        $newItemPrice += (float)$productModel->getFinalPrice() * (float)$depositPercent/100 * (int)$preOrderProduct['quantity'];
                    }
                    $entity->setCustomPrice($newItemPrice);
                    $entity->setOriginalCustomPrice($newItemPrice);
                    $entity->getProduct()->setIsSuperMode(true);
                }
            }
        }
    }

}