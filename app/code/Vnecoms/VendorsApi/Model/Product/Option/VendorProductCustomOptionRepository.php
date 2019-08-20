<?php

namespace Vnecoms\VendorsApi\Model\Product\Option;

use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Magento\Catalog\Model\Product\Option\Repository;
use Magento\Framework\Exception\LocalizedException;

class VendorProductCustomOptionRepository implements \Vnecoms\VendorsApi\Api\VendorProductCustomOptionRepositoryInterface
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
     * @var \Magento\Catalog\Model\Product\Option\Repository
     */
    protected $repository;

    /**
     * VendorProductCustomOptionRepository constructor.
     * @param ApiHelper $helper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param Repository $repository
     */
    public function __construct
    (
        ApiHelper $helper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        Repository $repository
    )
    {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->repository = $repository;
    }

    /**
     * Get the list of custom options for a specific product
     * @param int $customerId
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]
     */
    public function getList($customerId, $sku){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, true);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized.'));
        }
        return $this->repository->getList($sku);
    }

    /**
     * Get custom option for a specific product
     * @param int $customerId
     * @param string $sku
     * @param int $optionId
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface
     */
    public function get($customerId, $sku, $optionId){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized.'));
        }
        return $this->repository->get($sku, $optionId);
    }

    /**
     * Save Custom Option
     * @param int $customerId
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionInterface $option
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface
     */
    public function save($customerId, \Magento\Catalog\Api\Data\ProductCustomOptionInterface $option){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $productSku = $option->getProductSku();
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get($productSku);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized.'));
        }
        return $this->repository->save($option);
    }

    /**
     * @param int $customerId
     * @param string $sku
     * @param int $optionId
     * @return bool
     */
    public function deleteByIdentifier($customerId, $sku, $optionId){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, true);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized.'));
        }
        return $this->repository->deleteByIdentifier($sku, $optionId);
    }
}