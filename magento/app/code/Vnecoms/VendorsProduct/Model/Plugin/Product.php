<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\Plugin;

class Product
{
    /**
     * @var \Vnecoms\Vendors\Model\Vendor
     */
    protected $_vendor;
    
    public function __construct(\Vnecoms\Vendors\Model\VendorFactory $vendorFactory)
    {
        $this->_vendor = $vendorFactory->create();
    }
    /**
     * Set vendor object after the product is loaded.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int|string $modelId
     * @param string $field
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterLoad(\Magento\Catalog\Model\Product $product, $modelId, $field = null)
    {
        $vendorId = $product->getVendorId();
        $this->_vendor->load($vendorId);
        $product->setVendor($this->_vendor);
        return $product;
    }
}
