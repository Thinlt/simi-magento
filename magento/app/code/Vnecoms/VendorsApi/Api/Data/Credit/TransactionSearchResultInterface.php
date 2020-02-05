<?php

namespace Vnecoms\VendorsApi\Api\Data\Credit;


interface TransactionSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\TransactionInterface[] Array of collection items.
     */
    public function getItems();
    
    /**
     * Set items.
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Credit\TransactionInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
