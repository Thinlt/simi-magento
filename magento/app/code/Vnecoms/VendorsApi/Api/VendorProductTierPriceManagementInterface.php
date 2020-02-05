<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsApi\Api;

/**
 * @api
 * @deprecated 101.1.0 use ScopedProductTierPriceManagementInterface instead
 * @since 100.0.2
 */
interface VendorProductTierPriceManagementInterface
{
    /**
     * Create tier price for product
     *
     * @param int $customerId
     * @param string $sku
     * @param string $customerGroupId 'all' can be used to specify 'ALL GROUPS'
     * @param float $price
     * @param float $qty
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function add($customerId, $sku, $customerGroupId, $price, $qty);

    /**
     * Remove tier price from product
     *
     * @param int $customerId
     * @param string $sku
     * @param string $customerGroupId 'all' can be used to specify 'ALL GROUPS'
     * @param float $qty
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function remove($customerId, $sku, $customerGroupId, $qty);

    /**
     * Get tier price of product
     *
     * @param int $customerId
     * @param string $sku
     * @param string $customerGroupId 'all' can be used to specify 'ALL GROUPS'
     * @return \Magento\Catalog\Api\Data\ProductTierPriceInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($customerId, $sku, $customerGroupId);
}
