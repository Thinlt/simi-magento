<?php

namespace Vnecoms\VendorsApi\Api\Data\Sale;

/**
 * Vendor Memo interface.
 *
 * @api
 * @since 100.0.2
 */
interface MemoInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    /*
     * Entity ID.
     */
    const ENTITY_ID = 'entity_id';
    /*
     * Vendor order ID.
     */
    const VENDOR_ORDER_ID = 'vendor_order_id';
    /*
     * Increment ID.
     */
    const INCREMENT_ID = 'increment_id';
    /*
     * Created-at timestamp.
     */
    const CREATED_AT = 'created_at';
    /*
     * Updated-at timestamp.
     */
    const UPDATED_AT = 'updated_at';
    /*
     * Order ID.
     */
    const ORDER_ID = 'order_id';
    /*
     * Order increment ID.
     */
    const ORDER_INCREMENT_ID = 'order_increment_id';
    /*
     * Order Created-at.
     */
    const ORDER_CREATED_AT = 'order_created_at';
    /*
     * Billing name.
     */
    const BILLING_NAME = 'billing_name';
    /*
     * State.
     */
    const STATE = 'state';
    /*
     * Base grand total.
     */
    const BASE_GRAND_TOTAL = 'base_grand_total';
    /*
     * Order status.
     */
    const ORDER_STATUS = 'order_status';
    /*
     * Store Id.
     */
    const STORE_ID = 'store_id';
    /*
     * Billing address.
     */
    const BILLING_ADDRESS = 'billing_address';
    /*
     * Shipping address.
     */
    const SHIPPING_ADDRESS = 'shipping_address';
    /*
     * Customer name.
     */
    const CUSTOMER_NAME = 'customer_name';
    /*
     * Customer email.
     */
    const CUSTOMER_EMAIL = 'customer_email';
    /*
     * Customer group Id.
     */
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    /*
     * Payment method.
     */
    const PAYMENT_METHOD = 'payment_method';
    /*
     * Shipping information.
     */
    const SHIPPING_INFORMATION = 'shipping_information';
    /*
     * Subtotal.
     */
    const SUBTOTAL = 'subtotal';
    /*
     * Shipping and handling.
     */
    const SHIPPING_AND_HANDLING = 'shipping_and_handling';
    /*
     * Adjustment positive.
     */
    const ADJUSTMENT_POSITIVE = 'adjustment_positive';
    /*
     * Adjustment negative.
     */
    const ADJUSTMENT_NEGATIVE = 'adjustment_negative';
    /*
     * Order base grand total.
     */
    const ORDER_BASE_GRAND_TOTAL = 'order_base_grand_total';



    /**
     * Gets the ID for the memo.
     *
     * @return int|null Entity ID.
     */
    public function getEntityId();

    /**
     * Gets the vendor order ID for the memo.
     *
     * @return int|null External vendor order ID.
     */
    public function getVendorOrderId();

    /**
     * Gets the Increment ID for the memo.
     *
     * @return int|null External Increment ID.
     */
    public function getIncrementId();

    /**
     * Gets the created-at timestamp for the memo.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Gets the updated-at timestamp for the memo.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt();

    /**
     * Gets the Order ID for the memo.
     *
     * @return int|null External Order ID.
     */
    public function getOrderId();

    /**
     * Gets the Order Increment ID for the memo.
     *
     * @return int|null External Order Increment ID.
     */
    public function getOrderIncrementId();

    /**
     * Gets the Order Created-at for the memo.
     *
     * @return string|null External Order Created-at.
     */
    public function getOrderCreatedAt();

    /**
     * Gets the Billing name for the memo.
     *
     * @return string|null External Billing name.
     */
    public function getBillingName();

    /**
     * Gets the state for the memo.
     *
     * @return string|null State.
     */
    public function getState();

    /**
     * Gets the base grand total for the memo.
     *
     * @return float Base grand total.
     */
    public function getBaseGrandTotal();

    /**
     * Gets the Order status for the memo.
     *
     * @return float Order status.
     */
    public function getOrderStatus();

    /**
     * Gets the Store Id for the memo.
     *
     * @return float Store Id.
     */
    public function getStoreId();

    /**
     * Gets the Billing address for the memo.
     *
     * @return string|null External Billing address.
     */
    public function getBillingAddress();

    /**
     * Gets the Shipping address for the memo.
     *
     * @return string|null External Shipping address.
     */
    public function getShippingAddress();

    /**
     * Gets the Customer name for the memo.
     *
     * @return string|null External Customer name.
     */
    public function getCustomerName();

    /**
     * Gets the Customer email for the memo.
     *
     * @return string|null External Customer email.
     */
    public function getCustomerEmail();

    /**
     * Gets the Customer group Id for the memo.
     *
     * @return int|null External Customer group Id.
     */
    public function getCustomerGroupId();

    /**
     * Gets the Payment method for the memo.
     *
     * @return string|null External Payment method.
     */
    public function getPaymentMethod();

    /**
     * Gets the Shipping information for the memo.
     *
     * @return string|null External Shipping information.
     */
    public function getShippingInformation();

    /**
     * Gets the subtotal for the memo.
     *
     * @return float|null Subtotal.
     */
    public function getSubtotal();

    /**
     * Gets the Shipping and handling for the memo.
     *
     * @return string|null External Shipping and handling.
     */
    public function getShippingAndHandling();

    /**
     * Gets the Adjustment positive for the memo.
     *
     * @return string|null External Adjustment positive.
     */
    public function getAdjustmentPositive();

    /**
     * Gets the Adjustment negative for the memo.
     *
     * @return string|null External Adjustment negative.
     */
    public function getAdjustmentNegative();

    /**
     * Gets the Order base grand total for the memo.
     *
     * @return float Order base grand total.
     */
    public function getOrderBaseGrandTotal();







    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setEntityId($entityId);

    /**
     * Sets the vendor order ID for the memo.
     *
     * @param int $vendorOrderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setVendorOrderId($vendorOrderId);

    /**
     * Sets the Increment ID for the memo.
     *
     * @param int $incrementID
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setIncrementId($incrementID);

    /**
     * Sets the created-at timestamp for the memo.
     *
     * @param string $createdAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Sets the updated-at timestamp for the memo.
     *
     * @param string $timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setUpdatedAt($timestamp);

    /**
     * Sets the Order ID for the memo.
     *
     * @param int $orderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setOrderId($orderId);

    /**
     * Sets the Order Increment ID for the memo.
     *
     * @param int $orderIncrementID
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setOrderIncrementId($orderIncrementID);

    /**
     * Sets the Order created-at timestamp for the memo.
     *
     * @param string $orderCreatedAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setOrderCreatedAt($orderCreatedAt);

    /**
     * Sets the Billing name for the memo.
     *
     * @param string $billingName
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setBillingName($billingName);

    /**
     * Sets the state for the memo.
     *
     * @param string $state
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setState($state);

    /**
     * Sets the base grand total for the memo.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setBaseGrandTotal($amount);

    /**
     * Sets the Order status for the memo.
     *
     * @param string $orderStatus
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setOrderStatus($orderStatus);

    /**
     * Sets the Store ID for the memo.
     *
     * @param int $storeId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setStoreId($storeId);

    /**
     * Sets the Billing address for the memo.
     *
     * @param string $billingAddress
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setBillingAddress($billingAddress);

    /**
     * Sets the Shipping address for the memo.
     *
     * @param string $shippingAddress
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setShippingAddress($shippingAddress);

    /**
     * Sets the Customer name for the memo.
     *
     * @param string $customerName
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setCustomerName($customerName);

    /**
     * Sets the Customer email for the memo.
     *
     * @param string $customerEmail
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Sets the Customer group Id for the memo.
     *
     * @param int $customerGroupId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setCustomerGroupId($customerGroupId);

    /**
     * Sets the Payment method for the memo.
     *
     * @param string $paymentMethod
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * Sets the Shipping information for the memo.
     *
     * @param string $shippingInformation
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setShippingInformation($shippingInformation);

    /**
     * Sets the subtotal for the memo.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setSubtotal($amount);

    /**
     * Sets the Shipping and handling for the memo.
     *
     * @param string $shippingAndHandling
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setShippingAndHandling($shippingAndHandling);

    /**
     * Sets the Adjustment positive for the memo.
     *
     * @param string $adjustmentPositive
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setAdjustmentPositive($adjustmentPositive);

    /**
     * Sets the Adjustment negative for the memo.
     *
     * @param string $adjustmentNegative
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setAdjustmentNegative($adjustmentNegative);

    /**
     * Sets the Order base grand total for the memo.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\MemoInterface
     */
    public function setOrderBaseGrandTotal($amount);

}
