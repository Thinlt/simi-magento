<?php

namespace Simi\VendorMapping\Observer\Catalog\Product;

class LoadAfterVendor implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        if (!$product->getVendorId()) {// || $product->getIsAdminSell()) {
            $product->setVendorId('default');
        }
    }
}
