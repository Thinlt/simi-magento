<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsApi\Api;

/**
 * Tier prices storage.
 * @api
 * @since 101.1.0
 */
interface VendorTierPriceStorageInterface
{
    /**
     * Return product prices. In case of at least one of skus is not found exception will be thrown.
     *
     * @param int $customerId
     * @param string[] $skus
     * @return \Magento\Catalog\Api\Data\TierPriceInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @since 101.1.0
     */
    public function get($customerId, array $skus);

    /**
     * Add or update product prices.
     * If any items will have invalid price, price type, website id, sku, customer group or quantity, they will be
     * marked as failed and excluded from update list and \Magento\Catalog\Api\Data\PriceUpdateResultInterface[]
     * with problem description will be returned.
     * If there were no failed items during update empty array will be returned.
     * If error occurred during the update exception will be thrown.
     *
     * @param int $customerId
     * @param \Magento\Catalog\Api\Data\TierPriceInterface[] $prices
     * @return \Magento\Catalog\Api\Data\PriceUpdateResultInterface[]
     * @since 101.1.0
     */
    public function update($customerId, array $prices);

    /**
     * Remove existing tier prices and replace them with the new ones.
     * If any items will have invalid price, price type, website id, sku, customer group or quantity, they will be
     * marked as failed and excluded from replace list and \Magento\Catalog\Api\Data\PriceUpdateResultInterface[]
     * with problem description will be returned.
     * If there were no failed items during update empty array will be returned.
     * If error occurred during the update exception will be thrown.
     *
     * @param int $customerId
     * @param \Magento\Catalog\Api\Data\TierPriceInterface[] $prices
     * @return \Magento\Catalog\Api\Data\PriceUpdateResultInterface[]
     * @since 101.1.0
     */
    public function replace($customerId, array $prices);

    /**
     * Delete product tier prices.
     * If any items will have invalid price, price type, website id, sku, customer group or quantity, they will be
     * marked as failed and excluded from delete list and \Magento\Catalog\Api\Data\PriceUpdateResultInterface[]
     * with problem description will be returned.
     * If there were no failed items during update empty array will be returned.
     * If error occurred during the update exception will be thrown.
     *
     * @param int $customerId
     * @param \Magento\Catalog\Api\Data\TierPriceInterface[] $prices
     * @return \Magento\Catalog\Api\Data\PriceUpdateResultInterface[]
     * @since 101.1.0
     */
    public function delete($customerId, array $prices);
}
