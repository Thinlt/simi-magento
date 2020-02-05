<?php

namespace Vnecoms\VendorsApi\Api;

/**
 * Vendor CRUD interface.
 * @api
 */
interface VendorRepositoryInterface
{
    /**
     * Get customer by Customer ID.
     *
     * @param int $customerId
     * @return \Vnecoms\VendorsApi\Api\Data\VendorInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($customerId);
}
