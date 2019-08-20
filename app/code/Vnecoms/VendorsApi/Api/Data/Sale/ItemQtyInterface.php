<?php

namespace Vnecoms\VendorsApi\Api\Data\Sale;

/**
 * Vendor Item Qty interface.
 *
 * @api
 * @since 100.0.2
 */
interface ItemQtyInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    /*
     * Entity ID.
     */
    const ITEM_ID = 'item_id';
    /*
     * Vendor ID.
     */
    const QTY = 'qty';

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param int $id
     */
    public function setItemId($id);

    /**
     * @return float
     */
    public function getQty();

    /**
     * @param float $qty
     */
    public function setQty($qty);

}
