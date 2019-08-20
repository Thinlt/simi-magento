<?php

namespace Vnecoms\VendorsApi\Api;

/**
 * Vendor CRUD interface.
 * @api
 */
interface DashboardRepositoryInterface
{
    /**
     * @param int $customerId
     * @param string $period
     * @return \Vnecoms\VendorsApi\Api\Data\Dashboard\DashboardInterface
     */
    public function getDashboardInfo($customerId, $period = '7d');
}
