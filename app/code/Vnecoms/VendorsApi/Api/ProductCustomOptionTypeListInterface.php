<?php

namespace Vnecoms\VendorsApi\Api;

interface ProductCustomOptionTypeListInterface
{
    /**
     * Get custom option types
     * @param int $customerId
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionTypeInterface[]
     */
    public function getItems($customerId);
}