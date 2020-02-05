<?php
namespace Vnecoms\VendorsDashboard\Block\Dashboard\Product;

class NewProducts extends \Vnecoms\VendorsDashboard\Block\Vendors\Dashboard\Product\NewProducts
{
   /**
    * Get product list URL
    *
    * @return string
    */
    public function getProductListUrl()
    {
        return $this->getUrl('marketplace/catalog_product');
    }
   
   /**
    * Get Edit product url
    *
    * @param \Magento\Catalog\Model\Product $product
    * @return string
    */
    public function getEditUrl(\Magento\Catalog\Model\Product $product)
    {
        return $this->getUrl('marketplace/catalog_product/edit', ['id' => $product->getId()]);
    }
}
