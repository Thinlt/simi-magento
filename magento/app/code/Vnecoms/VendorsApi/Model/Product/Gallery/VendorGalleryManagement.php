<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsApi\Model\Product\Gallery;

use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Magento\Catalog\Model\Product\Gallery\GalleryManagement;
use Magento\Framework\Exception\LocalizedException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VendorGalleryManagement implements \Vnecoms\VendorsApi\Api\VendorProductAttributeMediaGalleryManagementInterface
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
     * @var \Magento\Catalog\Model\Product\Gallery\GalleryManagement
     */
    protected $galleryManagement;

    /**
     * VendorGalleryManagement constructor.
     * @param ApiHelper $helper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param GalleryManagement $galleryManagement
     */
    public function __construct(
        ApiHelper $helper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        GalleryManagement $galleryManagement
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->galleryManagement = $galleryManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function create($customerId, $sku, \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $entry)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized.'));
        }
        return $this->galleryManagement->create($sku, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function update($customerId, $sku, \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $entry)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized.'));
        }
        return $this->galleryManagement->update($sku, $entry);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($customerId, $sku, $entryId)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized.'));
        }
        return $this->galleryManagement->remove($sku, $entryId);
    }

    /**
     * {@inheritdoc}
     */
    public function get($customerId, $sku, $entryId)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized.'));
        }
        return $this->galleryManagement->get($sku, $entryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($customerId, $sku)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized.'));
        }
        return $this->galleryManagement->getList($sku);
    }
}
