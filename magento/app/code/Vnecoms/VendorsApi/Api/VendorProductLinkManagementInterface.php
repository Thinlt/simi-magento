<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsApi\Api;

/**
 * Interface for Management of ProductLink
 * @api
 * @since 100.0.2
 */
interface VendorProductLinkManagementInterface
{
    /**
     * Get all children for Bundle product
     *
     * @param int $customerId
     * @param string $productSku
     * @param int $optionId
     * @return \Magento\Bundle\Api\Data\LinkInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getChildren($customerId, $productSku, $optionId = null);

    /**
     * Add child product to specified Bundle option by product sku
     *
     * @param int $customerId
     * @param string $sku
     * @param int $optionId
     * @param \Magento\Bundle\Api\Data\LinkInterface $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @return int
     */
    public function addChildByProductSku($customerId, $sku, $optionId, \Magento\Bundle\Api\Data\LinkInterface $linkedProduct);

    /**
     * @param int $customerId
     * @param string $sku
     * @param \Magento\Bundle\Api\Data\LinkInterface $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     */
    public function saveChild(
        $customerId,
        $sku,
        \Magento\Bundle\Api\Data\LinkInterface $linkedProduct
    );

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $customerId
     * @param int $optionId
     * @param \Magento\Bundle\Api\Data\LinkInterface $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @return int
     */
    public function addChild(
        $customerId,
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $optionId,
        \Magento\Bundle\Api\Data\LinkInterface $linkedProduct
    );

    /**
     * Remove product from Bundle product option
     *
     * @param int $customerId
     * @param string $sku
     * @param int $optionId
     * @param string $childSku
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     */
    public function removeChild($customerId, $sku, $optionId, $childSku);
}
