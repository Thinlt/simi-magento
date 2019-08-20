<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsApi\Model\Product\Price;

use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\Product\TierPriceManagement;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VendorTierPriceManagement implements \Vnecoms\VendorsApi\Api\VendorProductTierPriceManagementInterface
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
     * @var \Magento\Catalog\Model\Product\TierPriceManagement
     */
    protected $tierPriceManagement;

    /**
     * VendorTierPriceManagement constructor.
     * @param ApiHelper $helper
     * @param ProductRepositoryInterface $productRepository
     * @param TierPriceManagement $tierPriceManagement
     */
    public function __construct(
        ApiHelper $helper,
        ProductRepositoryInterface $productRepository,
        TierPriceManagement $tierPriceManagement
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->tierPriceManagement = $tierPriceManagement;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function add($customerId, $sku, $customerGroupId, $price, $qty)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $sku));
        }
        return $this->tierPriceManagement->add($sku, $customerGroupId, $price, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($customerId, $sku, $customerGroupId, $qty)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $sku));
        }
        return $this->tierPriceManagement->remove($sku, $customerGroupId, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($customerId, $sku, $customerGroupId)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $sku));
        }
        return $this->tierPriceManagement->getList($sku, $customerGroupId);
    }
}
