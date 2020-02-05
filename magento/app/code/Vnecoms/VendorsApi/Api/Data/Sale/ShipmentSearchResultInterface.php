<?php

namespace Vnecoms\VendorsApi\Api\Data\Sale;


interface ShipmentSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}