<?php

namespace Vnecoms\VendorsApi\Api;

/**
 * Vendor CRUD interface.
 * @api
 */
interface ReportRepositoryInterface
{
    /**
     * @param int $customerId
     * @param int $limit
     * @return \Vnecoms\VendorsApi\Api\Data\Report\BestsellingSearchResultInterface
     */
    public function getBestSelling($customerId, $limit = 5);
    
    /**
     * @param int $customerId
     * @param int $limit
     * @return \Vnecoms\VendorsApi\Api\Data\Report\MostViewedSearchResultInterface
     */
    public function getMostViewed($customerId, $limit = 5);
    
}
