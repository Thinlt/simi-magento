<?php

namespace Vnecoms\VendorsApi\Api;

/**
 * Vendor CRUD interface.
 * @api
 */
interface OrderRepositoryInterface
{
    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\OrderSearchResultInterface
     */
    public function getList($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * @param int $customerId
     * @param int $orderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface
     */
    public function getOrder($customerId, $orderId);
    
    /**
     * @param int $customerId
     * @param int $orderId
     * @return bool
     */
    public function cancel($customerId, $orderId);
}
