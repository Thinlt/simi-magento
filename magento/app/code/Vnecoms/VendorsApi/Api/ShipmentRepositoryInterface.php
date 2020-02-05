<?php

namespace Vnecoms\VendorsApi\Api;

/**
 * Vendor CRUD interface.
 * @api
 */
interface ShipmentRepositoryInterface
{
    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentSearchResultInterface
     */
    public function getList($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param  int $customerId
     * @param  int $vendorOrderId
     * @param  \Vnecoms\VendorsApi\Api\Data\Sale\ItemQtyInterface[] $items
     * @param  string $comment
     * @param  \Vnecoms\VendorsApi\Api\Data\Sale\TrackingInterface[] $trackings
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createShipment(
        $customerId,
        $vendorOrderId,
        $items,
        $comment,
        $trackings
    );

}