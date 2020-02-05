<?php
namespace Vnecoms\Credit\Api;

/**
 * credit management service interface.
 * @api
 */
interface CreditManagementInterface
{
    /**
     * Returns information for used credit in a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return string The coupon code data.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function get($cartId);

    /**
     * Apply credit to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param float $creditAmount The credit amount.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified coupon could not be added.
     */
    public function set($cartId, $creditAmount);

    /**
     * Cancel the credit from a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotDeleteException The specified credit could not be removed.
     */
    public function remove($cartId);
}
