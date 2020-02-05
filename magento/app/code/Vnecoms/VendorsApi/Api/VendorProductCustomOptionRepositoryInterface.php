<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsApi\Api;

/**
 * @api
 * @since 100.0.2
 */
interface VendorProductCustomOptionRepositoryInterface
{
    /**
     * Get the list of custom options for a specific product
     * @param int $customerId
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]
     */
    public function getList($customerId, $sku);

    /**
     * Get custom option for a specific product
     * @param int $customerId
     * @param string $sku
     * @param int $optionId
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface
     */
    public function get($customerId, $sku, $optionId);

    /**
     * Save Custom Option
     * @param int $customerId
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionInterface $option
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface
     */
    public function save($customerId, \Magento\Catalog\Api\Data\ProductCustomOptionInterface $option);

    /**
     * @param int $customerId
     * @param string $sku
     * @param int $optionId
     * @return bool
     */
    public function deleteByIdentifier($customerId, $sku, $optionId);
}
