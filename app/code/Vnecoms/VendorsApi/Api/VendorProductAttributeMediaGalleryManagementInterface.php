<?php
/**
 * Product Media Attribute Write Service
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsApi\Api;

/**
 * @todo implement this interface as a \Magento\Catalog\Model\Product\Attribute\Media\GalleryManagement.
 * Move logic from service there.
 * @api
 * @since 100.0.2
 */
interface VendorProductAttributeMediaGalleryManagementInterface
{
    /**
     * Create new gallery entry
     *
     * @param int $customerId
     * @param string $sku
     * @param \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $entry
     * @return int gallery entry ID
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function create(
        $customerId,
        $sku,
        \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $entry
    );

    /**
     * Update gallery entry
     *
     * @param int $customerId
     * @param string $sku
     * @param \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $entry
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function update(
        $customerId,
        $sku,
        \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $entry
    );

    /**
     * Remove gallery entry
     *
     * @param int $customerId
     * @param string $sku
     * @param int $entryId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function remove(
        $customerId,
        $sku,
        $entryId
    );

    /**
     * Return information about gallery entry
     *
     * @param int $customerId
     * @param string $sku
     * @param int $entryId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface
     */
    public function get(
        $customerId,
        $sku,
        $entryId
    );

    /**
     * Retrieve the list of gallery entries associated with given product
     *
     * @param int $customerId
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface[]
     */
    public function getList(
        $customerId,
        $sku
    );
}
