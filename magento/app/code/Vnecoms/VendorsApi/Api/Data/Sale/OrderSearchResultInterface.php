<?php

namespace Vnecoms\VendorsApi\Api\Data\Sale;


interface OrderSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
