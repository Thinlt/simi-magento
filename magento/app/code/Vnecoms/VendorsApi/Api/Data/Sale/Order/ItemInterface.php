<?php

namespace Vnecoms\VendorsApi\Api\Data\Sale\Order;

interface ItemInterface extends \Magento\Sales\Api\Data\OrderItemInterface
{
    /*
     * Thumbnail
     */
    const THUMBNAIL = 'thumbnail';
    
    /**
     * Get Thumbnail
     *
     * @return string|null
     */
    public function getThumbnail();
    
    /**
     * Get items options
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\Order\ItemOptionInterface[]
     */
    public function getItemOptions();
}
