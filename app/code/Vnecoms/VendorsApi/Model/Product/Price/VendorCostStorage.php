<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsApi\Model\Product\Price;

use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Price\CostStorage;

/**
 * Product cost storage.
 */
class VendorCostStorage implements \Vnecoms\VendorsApi\Api\VendorCostStorageInterface
{
    /**
     * @var ApiHelper
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Model\Product\Price\CostStorage
     */
    protected $costStorage;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;


    /**
     * VendorCostStorage constructor.
     * @param ApiHelper $helper
     * @param ProductRepositoryInterface $productRepository
     * @param CostStorage $costStorage
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        ApiHelper $helper,
        ProductRepositoryInterface $productRepository,
        CostStorage $costStorage,
        CollectionFactory $productCollectionFactory
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->costStorage = $costStorage;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($customerId, array $skus)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        foreach ($skus as $sku){
            if ($this->productRepository->get($sku, ['edit_mode' => true])->getVendorId() != $vendorId){
                throw new LocalizedException(__('You are not authorized for product %1.', $sku));
            }
        }
        return $this->costStorage->get($skus);
    }

    /**
     * {@inheritdoc}
     */
    public function update($customerId, array $prices)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $productCollection = $this->productCollectionFactory->create();
        $productVendorCollection = $productCollection
            ->addAttributeToSelect('*')
            ->addFieldToFilter('vendor_id', $vendorId)
            ->load();
        $skuProductVendors = [];
        foreach ($productVendorCollection as $product){
            $skuProductVendors[] = $product['sku'];
        }

        foreach ($prices as $price){
            if (!in_array($price['sku'], $skuProductVendors)){
                throw new LocalizedException(__('You are not authorized for product %1.', $price['sku']));
            }
        }

        return $this->costStorage->update($prices);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($customerId, array $skus)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        foreach ($skus as $sku){
            if ($this->productRepository->get($sku, ['edit_mode' => true])->getVendorId() != $vendorId){
                throw new LocalizedException(__('You are not authorized for product %1.', $sku));
            }
        }

        return $this->costStorage->delete($skus);
    }
}
