<?php

namespace Vnecoms\VendorsApi\Api;

/**
 * Vendor CRUD interface.
 * @api
 */
interface WithdrawalRepositoryInterface
{
    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalSearchResultInterface
     */
    public function getList($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param  int $customerId
     * @param  \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface[] $withdrawal
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createWithdrawal(
        $customerId,
        $withdrawal
    );
}