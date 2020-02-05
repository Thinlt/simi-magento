<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Giftcard\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Gift Card search results
 * @api
 */
interface GiftcardHistorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Gift Card History list
     *
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterface[]
     */
    public function getItems();

    /**
     * Set Gift Card History list
     *
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
