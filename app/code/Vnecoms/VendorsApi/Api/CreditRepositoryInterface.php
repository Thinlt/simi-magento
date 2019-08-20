<?php

namespace Vnecoms\VendorsApi\Api;

/**
 * Vendor CRUD interface.
 * @api
 */
interface CreditRepositoryInterface
{
    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\TransactionSearchResultInterface
     */
    public function getTransactions($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
