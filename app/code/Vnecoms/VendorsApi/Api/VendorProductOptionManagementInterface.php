<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsApi\Api;

/**
 * Option manager for bundle products
 *
 * @api
 * @since 100.0.2
 */
interface VendorProductOptionManagementInterface
{
    /**
     * Add new option for bundle product
     * @param int $customerId
     * @param \Magento\Bundle\Api\Data\OptionInterface $option
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function save($customerId, \Magento\Bundle\Api\Data\OptionInterface $option);

    /**
     * Update option for bundle product
     * @param int $customerId
     * @param int $optionId
     * @param \Magento\Bundle\Api\Data\OptionInterface $option
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function update($customerId, $optionId, \Magento\Bundle\Api\Data\OptionInterface $option);
}
