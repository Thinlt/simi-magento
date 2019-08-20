<?php

namespace Vnecoms\VendorsApi\Api\Data\Sale;


interface MemoSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}