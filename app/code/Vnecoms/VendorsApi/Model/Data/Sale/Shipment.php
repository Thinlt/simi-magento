<?php

namespace Vnecoms\VendorsApi\Model\Data\Sale;

use Magento\Framework\Model\AbstractModel;

/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Shipment extends AbstractModel implements
    \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
{
    /**
     * Gets the ID for the shipment.
     *
     * @return int|null Shipment ID.
     */
    public function getEntityId(){
        return $this->_getData(self::ENTITY_ID);
    }

    /**
     * Gets the vendor order id for the shipment.
     *
     * @return int|null Vendor Order ID.
     */
    public function getVendorOrderId(){
        return $this->_getData(self::VENDOR_ORDER_ID);
    }

    /**
     * Gets the increment ID for the shipment.
     *
     * @return int|null Increment ID.
     */
    public function getIncrementId(){
        return $this->_getData(self::INCREMENT_ID);
    }

    /**
     * Gets the store ID for the shipment.
     *
     * @return int|null Store ID.
     */
    public function getStoreId(){
        return $this->_getData(self::STORE_ID);
    }

    /**
     * Gets the order increment ID for the shipment.
     *
     * @return int|null Order Increment ID.
     */
    public function getOrderIncrementId(){
        return $this->_getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * Gets the order ID for the shipment.
     *
     * @return int|null Order ID.
     */
    public function getOrderId(){
        return $this->_getData(self::ORDER_ID);
    }

    /**
     * Gets the Order Created-at for the shipment.
     *
     * @return string Order Created-at.
     */
    public function getOrderCreatedAt(){
        return $this->_getData(self::ORDER_CREATE_AT);
    }

    /**
     * Gets the Customer Name for the shipment.
     *
     * @return string Customer Name.
     */
    public function getCustomerName(){
        return $this->_getData(self::CUSTOMER_NAME);
    }

    /**
     * Gets the total quantity for the shipment.
     *
     * @return float|null Total quantity.
     */
    public function getTotalQty(){
        return $this->_getData(self::TOTAL_QTY);
    }

    /**
     * Gets the shipment status.
     *
     * @return int|null Shipment status.
     */
    public function getShipmentStatus(){
        return $this->_getData(self::SHIPMENT_STATUS);
    }

    /**
     * Gets the Order status.
     *
     * @return int|null Order status.
     */
    public function getOrderStatus(){
        return $this->_getData(self::ORDER_STATUS);
    }

    /**
     * Gets the billing address for the shipment.
     *
     * @return string Billing address.
     */
    public function getBillingAddress(){
        return $this->_getData(self::BILLING_ADDRESS);
    }

    /**
     * Gets the shipping address for the shipment.
     *
     * @return string Shipping address.
     */
    public function getShippingAddress(){
        return $this->_getData(self::SHIPPING_ADDRESS);
    }

    /**
     * Gets the billing name for the shipment.
     *
     * @return string Billing name.
     */
    public function getBillingName(){
        return $this->_getData(self::BILLING_NAME);
    }

    /**
     * Gets the shipping name for the shipment.
     *
     * @return string Shipping name.
     */
    public function getShippingName(){
        return $this->_getData(self::SHIPPING_NAME);
    }

    /**
     * Gets the Customer email for the shipment.
     *
     * @return string Customer email.
     */
    public function getCustomerEmail(){
        return $this->_getData(self::CUSTOMER_EMAIL);
    }

    /**
     * Gets the Customer group ID for the shipment.
     *
     * @return int|null Customer group ID.
     */
    public function getCustomerGroupId(){
        return $this->_getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * Gets the Payment method for the shipment.
     *
     * @return string Payment method.
     */
    public function getPaymentMethod(){
        return $this->_getData(self::PAYMENT_METHOD);
    }

    /**
     * Gets the shipping information for the shipment.
     *
     * @return string Shipping information.
     */
    public function getShippingInformation(){
        return $this->_getData(self::SHIPPING_INFORMATION);
    }

    /**
     * Gets the created-at timestamp for the shipment.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Gets the updated-at timestamp for the shipment.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt(){
        return $this->_getData(self::UPDATED_AT);
    }




    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setEntityId($entityId){
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Sets Vendor order ID.
     *
     * @param int $vendorOrderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setVendorOrderId($vendorOrderId){
        return $this->setData(self::VENDOR_ORDER_ID, $vendorOrderId);
    }

    /**
     * Sets the increment ID for the shipment.
     *
     * @param int $incrementId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setIncrementId($incrementId){
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * Sets the store ID for the shipment.
     *
     * @param int $storeId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setStoreId($storeId){
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Sets the Order increment ID for the shipment.
     *
     * @param int $orderIncrementId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setOrderIncrementId($orderIncrementId){
        return $this->setData(self::ORDER_INCREMENT_ID, $orderIncrementId);
    }

    /**
     * Sets the order ID for the shipment.
     *
     * @param int $orderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setOrderId($orderId){
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Sets the order created-at timestamp for the shipment.
     *
     * @param string $orderCreatedAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setOrderCreatedAt($orderCreatedAt){
        return $this->setData(self::ORDER_CREATE_AT, $orderCreatedAt);
    }

    /**
     * Sets the Customer Name for the shipment.
     *
     * @param string $customerName
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setCustomerName($customerName){
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * Sets the total quantity for the shipment.
     *
     * @param float $qty
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setTotalQty($qty){
        return $this->setData(self::TOTAL_QTY, $qty);
    }

    /**
     * Sets the shipment status.
     *
     * @param int $shipmentStatus
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setShipmentStatus($shipmentStatus){
        return $this->setData(self::SHIPMENT_STATUS, $shipmentStatus);
    }

    /**
     * Sets the Order status.
     *
     * @param int $orderStatus
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setOrderStatus($orderStatus){
        return $this->setData(self::ORDER_STATUS, $orderStatus);
    }

    /**
     * Sets the billing address for the shipment.
     *
     * @param string $billingAddress
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setBillingAddress($billingAddress){
        return $this->setData(self::BILLING_ADDRESS, $billingAddress);
    }

    /**
     * Sets the shipping address for the shipment.
     *
     * @param string $shippingAddress
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setShippingAddress($shippingAddress){
        return $this->setData(self::SHIPPING_ADDRESS, $shippingAddress);
    }

    /**
     * Sets the billing name for the shipment.
     *
     * @param string $billingName
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setBillingName($billingName){
        return $this->setData(self::BILLING_NAME, $billingName);
    }

    /**
     * Sets the shipping name for the shipment.
     *
     * @param string $shippingName
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setShippingName($shippingName){
        return $this->setData(self::SHIPPING_NAME, $shippingName);
    }

    /**
     * Sets the Customer Email for the shipment.
     *
     * @param string $customerEmail
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setCustomerEmail($customerEmail){
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * Sets the Customer group ID for the shipment.
     *
     * @param int $customerGroupId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setCustomerGroupId($customerGroupId){
        return $this->setData(self::CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * Sets the Payment method for the shipment.
     *
     * @param string $paymentMethod
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setPaymentMethod($paymentMethod){
        return $this->setData(self::PAYMENT_METHOD, $paymentMethod);
    }

    /**
     * Sets the shipping information for the shipment.
     *
     * @param string $shippingInformation
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setShippingInformation($shippingInformation){
        return $this->setData(self::SHIPPING_INFORMATION, $shippingInformation);
    }

    /**
     * Sets the created-at timestamp for the shipment.
     *
     * @param string $createdAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Sets the updated-at timestamp for the shipment.
     *
     * @param string $timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setUpdatedAt($timestamp){
        return $this->setData(self::UPDATED_AT, $timestamp);
    }

}