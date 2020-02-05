<?php

namespace Vnecoms\VendorsApi\Api\Data\Credit;


interface WithdrawalSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}