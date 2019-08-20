<?php

namespace Vnecoms\VendorsApi\Api\Data\Sale;

/**
 * Vendor Shipment interface.
 *
 * @api
 * @since 100.0.2
 */
interface ShipmentInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    /*
     * Entity ID.
     */
    const ENTITY_ID = 'entity_id';
    /*
     * Vendor order ID.
     */
    const VENDOR_ORDER_ID = 'vendor_order_id'; //add
    /*
     * Increment ID.
     */
    const INCREMENT_ID = 'increment_id';
    /*
     * Store ID.
     */
    const STORE_ID = 'store_id';
    /*
     * Order Increment ID.
     */
    const ORDER_INCREMENT_ID = 'order_increment_id'; //ADD
    /*
     * Order ID.
     */
    const ORDER_ID = 'order_id';
    /*
     * Order Created-at.
     */
    const ORDER_CREATE_AT = 'order_created_at'; //ADD
    /*
     * Customer Name.
     */
    const CUSTOMER_NAME = 'customer_name'; //ADD
    /*
     * Total quantity.
     */
    const TOTAL_QTY = 'total_qty';
    /*
     * Shipment status.
     */
    const SHIPMENT_STATUS = 'shipment_status';
    /*
     * Order status.
     */
    const ORDER_STATUS = 'order_status';//ADD
    /*
     * Billing address.
     */
    const BILLING_ADDRESS = 'billing_address'; //ADD
    /*
     * Shipping address.
     */
    const SHIPPING_ADDRESS = 'shipping_address'; //ADD
    /*
     * Billing Name.
     */
    const BILLING_NAME = 'billing_name'; //ADD
    /*
     * Shipping name.
     */
    const SHIPPING_NAME = 'shipping_name'; //ADD
    /*
     * Customer email.
     */
    const CUSTOMER_EMAIL = 'customer_email'; //ADD
    /*
     * Customer group ID.
     */
    const CUSTOMER_GROUP_ID = 'customer_group_id'; //ADD
    /*
     * Payment method.
     */
    const PAYMENT_METHOD = 'payment_method'; //ADD
    /*
     * Shipping information.
     */
    const SHIPPING_INFORMATION = 'shipping_information'; //ADD
    /*
     * Created-at timestamp.
     */
    const CREATED_AT = 'created_at';
    /*
     * Updated-at timestamp.
     */
    const UPDATED_AT = 'updated_at';




    /**
     * Gets the ID for the shipment.
     *
     * @return int|null Shipment ID.
     */
    public function getEntityId();

    /**
     * Gets the vendor order id for the shipment.
     *
     * @return int|null Vendor Order ID.
     */
    public function getVendorOrderId();

    /**
     * Gets the increment ID for the shipment.
     *
     * @return int|null Increment ID.
     */
    public function getIncrementId();

    /**
     * Gets the store ID for the shipment.
     *
     * @return int|null Store ID.
     */
    public function getStoreId();

    /**
     * Gets the order increment ID for the shipment.
     *
     * @return int|null Order Increment ID.
     */
    public function getOrderIncrementId();

    /**
     * Gets the order ID for the shipment.
     *
     * @return int|null Order ID.
     */
    public function getOrderId();

    /**
     * Gets the Order Created-at for the shipment.
     *
     * @return string Order Created-at.
     */
    public function getOrderCreatedAt();

    /**
     * Gets the Customer Name for the shipment.
     *
     * @return string Customer Name.
     */
    public function getCustomerName();

    /**
     * Gets the total quantity for the shipment.
     *
     * @return float|null Total quantity.
     */
    public function getTotalQty();

    /**
     * Gets the shipment status.
     *
     * @return int|null Shipment status.
     */
    public function getShipmentStatus();

    /**
     * Gets the Order status.
     *
     * @return int|null Order status.
     */
    public function getOrderStatus();

    /**
     * Gets the billing address for the shipment.
     *
     * @return string Billing address.
     */
    public function getBillingAddress();

    /**
     * Gets the shipping address for the shipment.
     *
     * @return string Shipping address.
     */
    public function getShippingAddress();

    /**
     * Gets the billing name for the shipment.
     *
     * @return string Billing name.
     */
    public function getBillingName();

    /**
     * Gets the shipping name for the shipment.
     *
     * @return string Shipping name.
     */
    public function getShippingName();

    /**
     * Gets the Customer email for the shipment.
     *
     * @return string Customer email.
     */
    public function getCustomerEmail();

    /**
     * Gets the Customer group ID for the shipment.
     *
     * @return int|null Customer group ID.
     */
    public function getCustomerGroupId();

    /**
     * Gets the Payment method for the shipment.
     *
     * @return string Payment method.
     */
    public function getPaymentMethod();

    /**
     * Gets the shipping information for the shipment.
     *
     * @return string Shipping information.
     */
    public function getShippingInformation();

    /**
     * Gets the created-at timestamp for the shipment.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Gets the updated-at timestamp for the shipment.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt();




    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setEntityId($entityId);

    /**
     * Sets Vendor order ID.
     *
     * @param int $vendorOrderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setVendorOrderId($vendorOrderId);

    /**
     * Sets the increment ID for the shipment.
     *
     * @param int $incrementId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setIncrementId($incrementId);

    /**
     * Sets the store ID for the shipment.
     *
     * @param int $storeId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setStoreId($storeId);

    /**
     * Sets the Order increment ID for the shipment.
     *
     * @param int $orderIncrementId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setOrderIncrementId($orderIncrementId);

    /**
     * Sets the order ID for the shipment.
     *
     * @param int $orderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setOrderId($orderId);

    /**
     * Sets the order created-at timestamp for the shipment.
     *
     * @param string $orderCreatedAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setOrderCreatedAt($orderCreatedAt);

    /**
     * Sets the Customer Name for the shipment.
     *
     * @param string $customerName
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setCustomerName($customerName);

    /**
     * Sets the total quantity for the shipment.
     *
     * @param float $qty
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setTotalQty($qty);

    /**
     * Sets the shipment status.
     *
     * @param int $shipmentStatus
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setShipmentStatus($shipmentStatus);

    /**
     * Sets the Order status.
     *
     * @param int $orderStatus
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setOrderStatus($orderStatus);

    /**
     * Sets the billing address for the shipment.
     *
     * @param string $billingAddress
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setBillingAddress($billingAddress);

    /**
     * Sets the shipping address for the shipment.
     *
     * @param string $shippingAddress
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setShippingAddress($shippingAddress);

    /**
     * Sets the billing name for the shipment.
     *
     * @param string $billingName
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setBillingName($billingName);

    /**
     * Sets the shipping name for the shipment.
     *
     * @param string $shippingName
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setShippingName($shippingName);

    /**
     * Sets the Customer Email for the shipment.
     *
     * @param string $customerEmail
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Sets the Customer group ID for the shipment.
     *
     * @param int $customerGroupId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setCustomerGroupId($customerGroupId);

    /**
     * Sets the Payment method for the shipment.
     *
     * @param string $paymentMethod
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * Sets the shipping information for the shipment.
     *
     * @param string $shippingInformation
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setShippingInformation($shippingInformation);

    /**
     * Sets the created-at timestamp for the shipment.
     *
     * @param string $createdAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Sets the updated-at timestamp for the shipment.
     *
     * @param string $timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ShipmentInterface
     */
    public function setUpdatedAt($timestamp);
}