<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsApi\Api;

/**
 * Interface ProductOptionRepositoryInterface
 * @api
 * @since 100.0.2
 */
interface VendorProductOptionRepositoryInterface
{
    /**
     * Get option for bundle product
     *
     * @param int $customerId
     * @param string $sku
     * @param int $optionId
     * @return \Magento\Bundle\Api\Data\OptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function get($customerId, $sku, $optionId);

    /**
     * Get all options for bundle product
     *
     * @param int $customerId
     * @param string $sku
     * @return \Magento\Bundle\Api\Data\OptionInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getList($customerId, $sku);

    /**
     * Remove bundle option
     *
     * @param int $customerId
     * @param \Magento\Bundle\Api\Data\OptionInterface $option
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function delete($customerId, \Magento\Bundle\Api\Data\OptionInterface $option);

    /**
     * Remove bundle option
     *
     * @param int $customerId
     * @param string $sku
     * @param int $optionId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function deleteById($customerId, $sku, $optionId);

    /**
     * Add new option for bundle product
     *
     * @param int $customerId
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param \Magento\Bundle\Api\Data\OptionInterface $option
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function save(
        $customerId,
        \Magento\Catalog\Api\Data\ProductInterface $product,
        \Magento\Bundle\Api\Data\OptionInterface $option
    );
}
