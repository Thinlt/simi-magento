<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsApi\Model\Product\Bundle;

use Magento\Framework\Exception\LocalizedException;
use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Magento\Bundle\Model\LinkManagement;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VendorProductLinkManagement implements \Vnecoms\VendorsApi\Api\VendorProductLinkManagementInterface
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
     * @var \Magento\Bundle\Model\LinkManagement
     */
    protected $linkManagement;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * VendorProductLinkManagement constructor.
     * @param ApiHelper $helper
     * @param ProductRepositoryInterface $productRepository
     * @param LinkManagement $linkManagement
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        ApiHelper $helper,
        ProductRepositoryInterface $productRepository,
        LinkManagement $linkManagement,
        CollectionFactory $productCollectionFactory
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->linkManagement = $linkManagement;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren($customerId, $productSku, $optionId = null)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($productSku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $productSku));
        }

        return $this->linkManagement->getChildren($productSku, $optionId);
    }

    /**
     * {@inheritdoc}
     */
    public function addChildByProductSku($customerId, $sku, $optionId, \Magento\Bundle\Api\Data\LinkInterface $linkedProduct)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $sku));
        }

        $productCollection = $this->productCollectionFactory->create();
        $productVendorCollection = $productCollection
            ->addAttributeToSelect('*')
            ->addFieldToFilter('vendor_id', $vendorId)
            ->load();
        $skuProductVendors = [];
        foreach ($productVendorCollection as $product){
            $skuProductVendors[] = $product['sku'];
        }

        if (!in_array($linkedProduct['sku'], $skuProductVendors)){
            throw new LocalizedException(__('You are not authorized for product %1.', $linkedProduct['sku']));
        }
        return $this->linkManagement->addChildByProductSku($sku, $optionId, $linkedProduct);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function saveChild(
        $customerId,
        $sku,
        \Magento\Bundle\Api\Data\LinkInterface $linkedProduct
    ) {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $sku));
        }
        return $this->linkManagement->saveChild($sku, $linkedProduct);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addChild(
        $customerId,
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $optionId,
        \Magento\Bundle\Api\Data\LinkInterface $linkedProduct
    ) {
        return $this->linkManagement->addChild($product, $optionId, $linkedProduct);
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild($customerId, $sku, $optionId, $childSku)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $sku));
        }
        return $this->linkManagement->removeChild($sku, $optionId, $childSku);
    }
}
