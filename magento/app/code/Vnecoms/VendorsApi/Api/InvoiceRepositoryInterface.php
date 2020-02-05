<?php

namespace Vnecoms\VendorsApi\Api;

/**
 * Vendor CRUD interface.
 * @api
 */
interface InvoiceRepositoryInterface
{
    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceSearchResultInterface
     */
    public function getList($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $customerId
     * @param  int $vendorOrderId
     * @param  \Vnecoms\VendorsApi\Api\Data\Sale\ItemQtyInterface[] $items
     * @param  string $comment
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createInvoice(
        $customerId,
        $vendorOrderId,
        $items,
        $comment
    );

}
