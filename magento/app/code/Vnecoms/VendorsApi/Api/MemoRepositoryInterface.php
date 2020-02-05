<?php

namespace Vnecoms\VendorsApi\Api;

/**
 * Vendor CRUD interface.
 * @api
 */
interface MemoRepositoryInterface
{
    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoSearchResultInterface
     */
    public function getList($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param  int $vendorOrderId
     * @param  \Vnecoms\VendorsApi\Api\Data\Sale\ItemQtyInterface[] $items
     * @param  string $comment
     * @param  int $doOffline
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createMemo(
        $vendorOrderId,
        $items,
        $comment,
        $doOffline
    );
}
