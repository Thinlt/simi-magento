<?php

namespace Vnecoms\VendorsApi\Model\Data\Sale;

use Magento\Framework\Model\AbstractModel;

/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Memo extends AbstractModel implements \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
{
    /**
     * Gets the Entity ID for the memo.
     *
     * @return int|null Order ID.
     */
    public function getEntityId(){
        return $this->_getData(self::ENTITY_ID);
    }

    /**
     * Gets the vendor order ID for the memo.
     *
     * @return int|null External vendor order ID.
     */
    public function getVendorOrderId(){
        return $this->_getData(self::VENDOR_ORDER_ID);
    }

    /**
     * Gets the Increment ID for the memo.
     *
     * @return int|null External Increment ID.
     */
    public function getIncrementId(){
        return $this->_getData(self::INCREMENT_ID);
    }

    /**
     * Gets the created-at timestamp for the memo.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Gets the updated-at timestamp for the memo.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt(){
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * Gets the Order ID for the memo.
     *
     * @return int|null External Order ID.
     */
    public function getOrderId(){
        return $this->_getData(self::ORDER_ID);
    }

    /**
     * Gets the Order Increment ID for the memo.
     *
     * @return int|null External Order Increment ID.
     */
    public function getOrderIncrementId(){
        return $this->_getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * Gets the Order Created-at for the memo.
     *
     * @return string|null External Order Created-at.
     */
    public function getOrderCreatedAt(){
        return $this->_getData(self::ORDER_CREATED_AT);
    }

    /**
     * Gets the Billing name for the memo.
     *
     * @return string|null External Billing name.
     */
    public function getBillingName(){
        return $this->_getData(self::BILLING_NAME);
    }

    /**
     * Gets the state for the memo.
     *
     * @return string|null State.
     */
    public function getState(){
        return $this->_getData(self::STATE);
    }

    /**
     * Gets the base grand total for the memo.
     *
     * @return float Base grand total.
     */
    public function getBaseGrandTotal(){
        return $this->_getData(self::BASE_GRAND_TOTAL);
    }

    /**
     * Gets the Order status for the memo.
     *
     * @return float Order status.
     */
    public function getOrderStatus(){
        return $this->_getData(self::ORDER_STATUS);
    }

    /**
     * Gets the Store Id for the memo.
     *
     * @return float Store Id.
     */
    public function getStoreId(){
        return $this->_getData(self::STORE_ID);
    }

    /**
     * Gets the Billing address for the memo.
     *
     * @return string|null External Billing address.
     */
    public function getBillingAddress(){
        return $this->_getData(self::BILLING_ADDRESS);
    }

    /**
     * Gets the Shipping address for the memo.
     *
     * @return string|null External Shipping address.
     */
    public function getShippingAddress(){
        return $this->_getData(self::SHIPPING_ADDRESS);
    }

    /**
     * Gets the Customer name for the memo.
     *
     * @return string|null External Customer name.
     */
    public function getCustomerName(){
        return $this->_getData(self::CUSTOMER_NAME);
    }

    /**
     * Gets the Customer email for the memo.
     *
     * @return string|null External Customer email.
     */
    public function getCustomerEmail(){
        return $this->_getData(self::CUSTOMER_EMAIL);
    }

    /**
     * Gets the Customer group Id for the memo.
     *
     * @return int|null External Customer group Id.
     */
    public function getCustomerGroupId(){
        return $this->_getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * Gets the Payment method for the memo.
     *
     * @return string|null External Payment method.
     */
    public function getPaymentMethod(){
        return $this->_getData(self::PAYMENT_METHOD);
    }

    /**
     * Gets the Shipping information for the memo.
     *
     * @return string|null External Shipping information.
     */
    public function getShippingInformation(){
        return $this->_getData(self::SHIPPING_INFORMATION);
    }

    /**
     * Gets the subtotal for the memo.
     *
     * @return float|null Subtotal.
     */
    public function getSubtotal(){
        return $this->_getData(self::SUBTOTAL);
    }

    /**
     * Gets the Shipping and handling for the memo.
     *
     * @return string|null External Shipping and handling.
     */
    public function getShippingAndHandling(){
        return $this->_getData(self::SHIPPING_AND_HANDLING);
    }

    /**
     * Gets the Adjustment positive for the memo.
     *
     * @return string|null External Adjustment positive.
     */
    public function getAdjustmentPositive(){
        return $this->_getData(self::ADJUSTMENT_POSITIVE);
    }

    /**
     * Gets the Adjustment negative for the memo.
     *
     * @return string|null External Adjustment negative.
     */
    public function getAdjustmentNegative(){
        return $this->_getData(self::ADJUSTMENT_NEGATIVE);
    }

    /**
     * Gets the Order base grand total for the memo.
     *
     * @return float Order base grand total.
     */
    public function getOrderBaseGrandTotal(){
        return $this->_getData(self::ORDER_BASE_GRAND_TOTAL);
    }




    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setEntityId($entityId){
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Sets the vendor order ID for the memo.
     *
     * @param int $vendorOrderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setVendorOrderId($vendorOrderId){
        return $this->setData(self::VENDOR_ORDER_ID, $vendorOrderId);
    }

    /**
     * Sets the Increment ID for the memo.
     *
     * @param int $incrementID
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setIncrementId($incrementID){
        return $this->setData(self::INCREMENT_ID, $incrementID);
    }

    /**
     * Sets the created-at timestamp for the memo.
     *
     * @param string $createdAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Sets the updated-at timestamp for the memo.
     *
     * @param string $timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setUpdatedAt($timestamp){
        return $this->setData(self::UPDATED_AT, $timestamp);
    }

    /**
     * Sets the Order ID for the memo.
     *
     * @param int $orderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setOrderId($orderId){
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Sets the Order Increment ID for the memo.
     *
     * @param int $orderIncrementID
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setOrderIncrementId($orderIncrementID){
        return $this->setData(self::ORDER_INCREMENT_ID, $orderIncrementID);
    }

    /**
     * Sets the Order created-at timestamp for the memo.
     *
     * @param string $orderCreatedAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setOrderCreatedAt($orderCreatedAt){
        return $this->setData(self::ORDER_CREATED_AT, $orderCreatedAt);
    }

    /**
     * Sets the Billing name for the memo.
     *
     * @param string $billingName
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setBillingName($billingName){
        return $this->setData(self::BILLING_NAME, $billingName);
    }

    /**
     * Sets the state for the memo.
     *
     * @param string $state
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setState($state){
        return $this->setData(self::STATE, $state);
    }

    /**
     * Sets the base grand total for the memo.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setBaseGrandTotal($amount){
        return $this->setData(self::BASE_GRAND_TOTAL, $amount);
    }

    /**
     * Sets the Order status for the memo.
     *
     * @param string $orderStatus
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setOrderStatus($orderStatus){
        return $this->setData(self::ORDER_STATUS, $orderStatus);
    }

    /**
     * Sets the Store ID for the memo.
     *
     * @param int $storeId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setStoreId($storeId){
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Sets the Billing address for the memo.
     *
     * @param string $billingAddress
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setBillingAddress($billingAddress){
        return $this->setData(self::BILLING_ADDRESS, $billingAddress);
    }

    /**
     * Sets the Shipping address for the memo.
     *
     * @param string $shippingAddress
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setShippingAddress($shippingAddress){
        return $this->setData(self::SHIPPING_ADDRESS, $shippingAddress);
    }

    /**
     * Sets the Customer name for the memo.
     *
     * @param string $customerName
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setCustomerName($customerName){
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * Sets the Customer email for the memo.
     *
     * @param string $customerEmail
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setCustomerEmail($customerEmail){
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * Sets the Customer group Id for the memo.
     *
     * @param int $customerGroupId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setCustomerGroupId($customerGroupId){
        return $this->setData(self::CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * Sets the Payment method for the memo.
     *
     * @param string $paymentMethod
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setPaymentMethod($paymentMethod){
        return $this->setData(self::PAYMENT_METHOD, $paymentMethod);
    }

    /**
     * Sets the Shipping information for the memo.
     *
     * @param string $shippingInformation
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setShippingInformation($shippingInformation){
        return $this->setData(self::SHIPPING_INFORMATION, $shippingInformation);
    }

    /**
     * Sets the subtotal for the memo.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setSubtotal($amount){
        return $this->setData(self::SUBTOTAL, $amount);
    }

    /**
     * Sets the Shipping and handling for the memo.
     *
     * @param string $shippingAndHandling
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setShippingAndHandling($shippingAndHandling){
        return $this->setData(self::SHIPPING_AND_HANDLING, $shippingAndHandling);
    }

    /**
     * Sets the Adjustment positive for the memo.
     *
     * @param string $adjustmentPositive
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setAdjustmentPositive($adjustmentPositive){
        return $this->setData(self::ADJUSTMENT_POSITIVE, $adjustmentPositive);
    }

    /**
     * Sets the Adjustment negative for the memo.
     *
     * @param string $adjustmentNegative
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setAdjustmentNegative($adjustmentNegative){
        return $this->setData(self::ADJUSTMENT_NEGATIVE, $adjustmentNegative);
    }

    /**
     * Sets the Order base grand total for the memo.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setOrderBaseGrandTotal($amount){
        return $this->setData(self::ORDER_BASE_GRAND_TOTAL, $amount);
    }

}


