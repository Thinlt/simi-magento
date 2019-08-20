<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsApi\Model\Product\Price;

use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\Product\Price\TierPriceStorage;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Tier price storage.
 */
class VendorTierPriceStorage implements \Vnecoms\VendorsApi\Api\VendorTierPriceStorageInterface
{
    /**
     * @var ApiHelper
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\Product\Price\TierPriceStorage
     */
    protected $tierPriceStorage;

    /**
     * VendorTierPriceStorage constructor.
     * @param ApiHelper $helper
     * @param ProductRepositoryInterface $productRepository
     * @param TierPriceStorage $tierPriceStorage
     */
    public function __construct(
        ApiHelper $helper,
        ProductRepositoryInterface $productRepository,
        TierPriceStorage $tierPriceStorage
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->tierPriceStorage = $tierPriceStorage;
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
        return $this->tierPriceStorage->get($skus);
    }

    /**
     * {@inheritdoc}
     */
    public function update($customerId, array $prices)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();

        $skus = array_unique(
            array_map(function ($price) {
                return $price->getSku();
            }, $prices)
        );

        foreach ($skus as $sku){
            if ($this->productRepository->get($sku, ['edit_mode' => true])->getVendorId() != $vendorId){
                throw new LocalizedException(__('You are not authorized for product %1.', $sku));
            }
        }
        return $this->tierPriceStorage->update($prices);
    }

    /**
     * {@inheritdoc}
     */
    public function replace($customerId, array $prices)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();

        foreach ($prices as $price){
            if ($this->productRepository->get($price['sku'], ['edit_mode' => true])->getVendorId() != $vendorId){
                throw new LocalizedException(__('You are not authorized for product %1.', $price['sku']));
            }
        }
        return $this->tierPriceStorage->replace($prices);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($customerId, array $prices)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();

        foreach ($prices as $price){
            if ($this->productRepository->get($price['sku'], ['edit_mode' => true])->getVendorId() != $vendorId){
                throw new LocalizedException(__('You are not authorized for product %1.', $price['sku']));
            }
        }
        return $this->tierPriceStorage->delete($prices);
    }
}
