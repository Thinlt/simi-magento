<?php

namespace Vnecoms\VendorsApi\Api\Data\Sale;

/**
 * Order interface.
 *
 * An order is a document that a web store issues to a customer. Magento generates a sales order that lists the product
 * items, billing and shipping addresses, and shipping and payment methods. A corresponding external document, known as
 * a purchase order, is emailed to the customer.
 * @api
 * @since 100.0.2
 */
interface OrderInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    /*
     * Entity ID.
     */
    const ENTITY_ID = 'entity_id';
    
    /*
     * Entity ID.
     */
    const INCREMENT_ID = 'increment_id';
    
    /*
     * Vendor ID.
     */
    const VENDOR_ID = 'vendor_id';
    /*
     * Order ID.
     */
    const ORDER_ID = 'order_id';
    /*
     * Status.
     */
    const STATUS = 'status';
    /*
     * Shipping description.
     */
    const SHIPPING_DESCRIPTION = 'shipping_description';
    /*
     * Base discount amount.
     */
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
    /*
     * Base grand total.
     */
    const BASE_GRAND_TOTAL = 'base_grand_total';
    /*
     * Base shipping invoiced.
     */
    const BASE_SHIPPING_INVOICED = 'base_shipping_invoiced';
    /*
     * Base shipping refunded.
     */
    const BASE_SHIPPING_REFUNDED = 'base_shipping_refunded';
    /*
     * Base shipping amount.
     */
    const BASE_SHIPPING_AMOUNT = 'base_shipping_amount';
    /*
     * Base shipping tax amount.
     */
    const BASE_SHIPPING_TAX_AMOUNT = 'base_shipping_tax_amount';
    /*
     * Base shipping tax refunded.
     */
    const BASE_SHIPPING_TAX_REFUNDED = 'base_shipping_tax_refunded';
    
    /*
     * Base subtotal.
     */
    const BASE_SUBTOTAL = 'base_subtotal';
    /*
     * Base total invoiced.
     */
    const BASE_TOTAL_INVOICED = 'base_total_invoiced';
    /*
     * Base total paid.
     */
    const BASE_TOTAL_PAID = 'base_total_paid';
    /*
     * Base total refunded.
     */
    const BASE_TOTAL_REFUNDED = 'base_total_refunded';
    /*
     * Base tax amount.
     */
    const BASE_TAX_AMOUNT = 'base_tax_amount';
    /*
     * Discount amount.
     */
    const DISCOUNT_AMOUNT = 'discount_amount';
    /*
     * Grand total.
     */
    const GRAND_TOTAL = 'grand_total';
    /*
     * Shipping amount.
     */
    const SHIPPING_AMOUNT = 'shipping_amount';
    /*
     * Shipping invoiced.
     */
    const SHIPPING_INVOICED = 'shipping_invoiced';
    /*
     * Shipping refunded.
     */
    const SHIPPING_REFUNDED = 'shipping_refunded';
    /*
     * Shipping tax amount.
     */
    const SHIPPING_TAX_AMOUNT = 'shipping_tax_amount';
    /*
     * Shipping tax refunded.
     */
    const SHIPPING_TAX_REFUNDED = 'shipping_tax_refunded';
    /*
     * Subtotal.
     */
    const SUBTOTAL = 'subtotal';
    /*
     * Tax amount.
     */
    const TAX_AMOUNT = 'tax_amount';
    /*
     * Total invoiced.
     */
    const TOTAL_INVOICED = 'total_invoiced';
    /*
     * Total paid.
     */
    const TOTAL_PAID = 'total_paid';
    /*
     * Total quantity ordered.
     */
    const TOTAL_QTY_ORDERED = 'total_qty_ordered';
    /*
     * Total refunded.
     */
    const TOTAL_REFUNDED = 'total_refunded';
    /*
     * Tax refunded.
     */
    const TAX_REFUNDED = 'tax_refunded';
    /*
     * Base tax refunded.
     */
    const BASE_TAX_REFUNDED = 'base_tax_refunded';
    /*
     * Tax invoiced.
     */
    const TAX_INVOICED = 'tax_invoiced';
    /*
     * Base tax invoiced.
     */
    const BASE_TAX_INVOICED = 'base_tax_invoiced';
    /*
     * Tax canceled.
     */
    const TAX_CANCELED = 'tax_canceled';
    /*
     * Base tax canceled.
     */
    const BASE_TAX_CANCELED = 'base_tax_canceled';
    /*
     * Subtotal including tax.
     */
    const SUBTOTAL_INCL_TAX = 'subtotal_incl_tax';
    /*
     * Base subtotal including tax.
     */
    const BASE_SUBTOTAL_INCL_TAX = 'base_subtotal_incl_tax';
    /*
     * Weight.
     */
    const WEIGHT = 'weight';
    /*
     * Shipping Method.
     */
    const SHIPPING_METHOD = 'shipping_method';
    /*
     * Created-at timestamp.
     */
    const CREATED_AT = 'created_at';
    /*
     * Updated-at timestamp.
     */
    const UPDATED_AT = 'updated_at';
    /*
     * Shipping including tax.
     */
    const SHIPPING_INCL_TAX = 'shipping_incl_tax';
    /*
     * Base shipping including tax.
     */
    const BASE_SHIPPING_INCL_TAX = 'base_shipping_incl_tax';
    /*
     * Total due.
     */
    const TOTAL_DUE = 'total_due';
    /*
     * Base total due.
     */
    const BASE_TOTAL_DUE = 'base_total_due';
    /*
     * Bill to name.
     */
    const BILL_TO_NAME = 'billing_name';
    /*
     * Ship to name.
     */
    const SHIPPING_TO_NAME = 'shipping_name';
    /*
     * Billing address.
     */
    const BILLING_ADDRESS = 'billing_address';
    /*
     * Shipping address.
     */
    const SHIPPING_ADDRESS = 'shipping_address';
    /*
     * Customer email.
     */
    const CUSTOMER_EMAIL = 'customer_email';
    /*
     * Customer group.
     */
        const CUSTOMER_GROUP = 'customer_group';
    /*
     * Shipping and handling.
     */
    const SHIPPING_AND_HANDLING = 'shipping_and_handling';
    /*
     * Customer name.
     */
    const CUSTOMER_NAME = 'customer_name';
    /*
     * Payment method.
     */
    const PAYMENT_METHOD = 'payment_method';
    
    /*
     * Payment method.
     */
    const PAYMENT = 'payment';
    
    /*
     * Items list
     */
    const ITEMS = 'items';
    
    /*
     * Can cancel
     */
    const CAN_CANCEL = 'can_cancel';
    
    /*
     * Can invoice
     */
    const CAN_INVOICE = 'can_invoice';
    
    /*
     * Can ship
     */
    const CAN_SHIP = 'can_ship';
    
    /*
     * Can credit memo
     */
    const CAN_CREDIT_MEMO = 'can_credit_memo';
    
    /*
     * Base currency code
     */
    const BASE_CURRENCY_CODE = 'base_currency_code';
    
    /*
     * Order currency code
     */
    const ORDER_CURRENCY_CODE = 'order_currency_code';


    /**
     * Gets the ID for the order.
     *
     * @return int|null Order ID.
     */
    public function getEntityId();

    /**
     * Gets the Vendor ID for the order.
     *
     * @return int|null Order ID.
     */
    public function getVendorId();

    /**
     * Gets the order ID for the order.
     *
     * @return int|null External order ID.
     */
    public function getOrderId();

    /**
     * Gets the status for the order.
     *
     * @return string|null Status.
     */
    public function getStatus();

    /**
     * Gets the shipping description for the order.
     *
     * @return string|null Shipping description.
     */
    public function getShippingDescription();

    /**
     * Gets the base discount amount for the order.
     *
     * @return float|null Base discount amount.
     */
    public function getBaseDiscountAmount();

    /**
     * Gets the base grand total for the order.
     *
     * @return float Base grand total.
     */
    public function getBaseGrandTotal();

    /**
     * Gets the base shipping invoiced amount for the order.
     *
     * @return float|null Base shipping invoiced.
     */
    public function getBaseShippingInvoiced();

    /**
     * Gets the base shipping refunded amount for the order.
     *
     * @return float|null Base shipping refunded.
     */
    public function getBaseShippingRefunded();

    /**
     * Gets the base shipping amount for the order.
     *
     * @return float|null Base shipping amount.
     */
    public function getBaseShippingAmount();

    /**
     * Gets the base shipping tax amount for the order.
     *
     * @return float|null Base shipping tax amount.
     */
    public function getBaseShippingTaxAmount();

    /**
     * Gets the base shipping tax refunded amount for the order.
     *
     * @return float|null Base shipping tax refunded.
     */
    public function getBaseShippingTaxRefunded();

    /**
     * Gets the base subtotal for the order.
     *
     * @return float|null Base subtotal.
     */
    public function getBaseSubtotal();

    /**
     * Gets the base total invoiced amount for the order.
     *
     * @return float|null Base total invoiced.
     */
    public function getBaseTotalInvoiced();

    /**
     * Gets the base total paid for the order.
     *
     * @return float|null Base total paid.
     */
    public function getBaseTotalPaid();

    /**
     * Gets the base total refunded amount for the order.
     *
     * @return float|null Base total refunded.
     */
    public function getBaseTotalRefunded();

    /**
     * Gets the base tax amount for the order.
     *
     * @return float|null Base tax amount.
     */
    public function getBaseTaxAmount();

    /**
     * Gets the discount amount for the order.
     *
     * @return float|null Discount amount.
     */
    public function getDiscountAmount();

    /**
     * Gets the grand total for the order.
     *
     * @return float Grand total.
     */
    public function getGrandTotal();

    /**
     * Gets the shipping amount for the order.
     *
     * @return float|null Shipping amount.
     */
    public function getShippingAmount();

    /**
     * Gets the shipping invoiced amount for the order.
     *
     * @return float|null Shipping invoiced amount.
     */
    public function getShippingInvoiced();

    /**
     * Gets the shipping refunded amount for the order.
     *
     * @return float|null Shipping refunded amount.
     */
    public function getShippingRefunded();

    /**
     * Gets the shipping tax amount for the order.
     *
     * @return float|null Shipping tax amount.
     */
    public function getShippingTaxAmount();

    /**
     * Gets the shipping tax refunded amount for the order.
     *
     * @return float|null Shipping tax refunded amount.
     */
    public function getShippingTaxRefunded();

    /**
     * Gets the subtotal for the order.
     *
     * @return float|null Subtotal.
     */
    public function getSubtotal();

    /**
     * Gets the tax amount for the order.
     *
     * @return float|null Tax amount.
     */
    public function getTaxAmount();

    /**
     * Gets the total invoiced amount for the order.
     *
     * @return float|null Total invoiced amount.
     */
    public function getTotalInvoiced();

    /**
     * Gets the total paid for the order.
     *
     * @return float|null Total paid.
     */
    public function getTotalPaid();

    /**
     * Gets the total quantity ordered for the order.
     *
     * @return float|null Total quantity ordered.
     */
    public function getTotalQtyOrdered();

    /**
     * Gets the total amount refunded amount for the order.
     *
     * @return float|null Total amount refunded.
     */
    public function getTotalRefunded();

    /**
     * Gets the tax refunded amount for the order.
     *
     * @return float|null Tax refunded amount.
     */
    public function getTaxRefunded();

    /**
     * Gets the base tax refunded amount for the order.
     *
     * @return float|null Base tax refunded.
     */
    public function getBaseTaxRefunded();

    /**
     * Gets the tax invoiced amount for the order.
     *
     * @return float|null Tax invoiced amount.
     */
    public function getTaxInvoiced();

    /**
     * Gets the base tax invoiced amount for the order.
     *
     * @return float|null Base tax invoiced.
     */
    public function getBaseTaxInvoiced();

    /**
     * Gets the tax canceled amount for the order.
     *
     * @return float|null Tax canceled amount.
     */
    public function getTaxCanceled();

    /**
     * Gets the base tax canceled for the order.
     *
     * @return float|null Base tax canceled.
     */
    public function getBaseTaxCanceled();

    /**
     * Gets the subtotal including tax amount for the order.
     *
     * @return float|null Subtotal including tax amount.
     */
    public function getSubtotalInclTax();

    /**
     * Gets the base subtotal including tax for the order.
     *
     * @return float|null Base subtotal including tax.
     */
    public function getBaseSubtotalInclTax();

    /**
     * Gets the weight for the order.
     *
     * @return float|null Weight.
     */
    public function getWeight();

    /**
     * Gets the shipping method for the order.
     *
     * @return string|null Base shipping amount.
     */
    public function getShippingMethod();

    /**
     * Gets the created-at timestamp for the order.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Gets the updated-at timestamp for the order.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt();

    /**
     * Gets the shipping including tax amount for the order.
     *
     * @return float|null Shipping including tax amount.
     */
    public function getShippingInclTax();

    /**
     * Gets the base shipping including tax for the order.
     *
     * @return float|null Base shipping including tax.
     */
    public function getBaseShippingInclTax();

    /**
     * Gets the total due for the order.
     *
     * @return float|null Total due.
     */
    public function getTotalDue();

    /**
     * Gets the base total due for the order.
     *
     * @return float|null Base total due.
     */
    public function getBaseTotalDue();

    /**
     * Gets the bill to name for the order.
     *
     * @return string|null Bill to name
     */
    public function getBillingName();

    /**
     * Gets the shipping to name for the order.
     *
     * @return string|null Shipping to name.
     */
    public function getShippingName();

    /**
     * Retrieve order billing address
     *
     * @return \Magento\Sales\Api\Data\OrderAddressInterface|null
     */
    public function getBillingAddress();

    /**
     * Retrieve order shipping address
     *
     * @return \Magento\Sales\Api\Data\OrderAddressInterface|null
     */
    public function getShippingAddress();

    /**
     * Gets the customer email for the order.
     *
     * @return string|null Customer email.
     */
    public function getCustomerEmail();

    /**
     * Gets the customer group for the order.
     *
     * @return string|null Customer group.
     */
    public function getCustomerGroup();

    /**
     * Gets the shipping and handling for the order.
     *
     * @return string|null Shipping and handling.
     */
    public function getShippingAndHandling();

    /**
     * Gets the customer name for the order.
     *
     * @return string|null Customer name.
     */
    public function getCustomerName();

    /**
     * Gets the payment method for the order.
     *
     * @return string|null Payment method.
     */
    public function getPaymentMethod();
    
    /**
     * Get base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode();
    
    /**
     * Get order currency code
     *
     * @return string
    */
    public function getOrderCurrencyCode();
    
    /**
     * Gets items for the order.
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\Order\ItemInterface[] Array of items.
     */
    public function getItems();
    
    /**
     * Sets items for the order.
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Sale\Order\ItemInterface[] $items
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface
     */
    public function setItems($items);
    
    /**
     * Gets order payment
     *
     * @return \Magento\Sales\Api\Data\OrderPaymentInterface|null
     */
    public function getPayment();
    
    /**
     * Sets order payment
     *
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface|null $payment
     * @return \Magento\Sales\Api\Data\OrderPaymentInterface
     */
    public function setPayment(\Magento\Sales\Api\Data\OrderPaymentInterface $payment = null);
    
    /**
     * Gets can cancel
     *
     * @return bool
     */
    public function getCanCancel();
    
    /**
     * Sets can cancel
     *
     * @param bool $canCancel
     * @return OrderInterface
    */
    public function setCanCancel($canCancel);
    
    /**
     * Gets can invoice
     *
     * @return bool
     */
    public function getCanInvoice();
    
    /**
     * Sets can invoice
     *
     * @param bool $canInvoice
     * @return OrderInterface
    */
    public function setCanInvoice($canInvoice);
    
    /**
     * Gets can ship
     *
     * @return bool
     */
    public function getCanShip();
    
    /**
     * Sets can Ship
     *
     * @param bool $canShip
     * @return OrderInterface
    */
    public function setCanShip($canShip);
    
    /**
     * Gets can credit memo
     *
     * @return bool
     */
    public function getCanCreditMemo();
    
    /**
     * Sets can Credit memo
     *
     * @param bool
     * @return OrderInterface
    */
    public function setCanCreditMemo($canCreditMemo);
    

    /**
     * Get increment id
     *
     * @return string
     */
    public function getIncrementId();

    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Sets the Vendor ID for the order.
     *
     * @param int $vendorId
     * @return $this
     */
    public function setVendorId($vendorId);

    /**
     * Sets the order ID for the order.
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Sets the status for the order.
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Sets the shipping description for the order.
     *
     * @param string $description
     * @return $this
     */
    public function setShippingDescription($description);

    /**
     * Sets the base discount amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseDiscountAmount($amount);

    /**
     * Sets the base grand total for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseGrandTotal($amount);

    /**
     * Sets the base shipping invoiced amount for the order.
     *
     * @param float $baseShippingInvoiced
     * @return $this
     */
    public function setBaseShippingInvoiced($baseShippingInvoiced);

    /**
     * Sets the base shipping refunded amount for the order.
     *
     * @param float $baseShippingRefunded
     * @return $this
     */
    public function setBaseShippingRefunded($baseShippingRefunded);

    /**
     * Sets the base shipping amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseShippingAmount($amount);

    /**
     * Sets the base shipping tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseShippingTaxAmount($amount);

    /**
     * Sets the base shipping tax refunded amount for the order.
     *
     * @param float $baseShippingTaxRefunded
     * @return $this
     */
    public function setBaseShippingTaxRefunded($baseShippingTaxRefunded);

    /**
     * Sets the base subtotal for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseSubtotal($amount);

    /**
     * Sets the base total invoiced amount for the order.
     *
     * @param float $baseTotalInvoiced
     * @return $this
     */
    public function setBaseTotalInvoiced($baseTotalInvoiced);

    /**
     * Sets the base total paid for the order.
     *
     * @param float $baseTotalPaid
     * @return $this
     */
    public function setBaseTotalPaid($baseTotalPaid);

    /**
     * Sets the base total refunded amount for the order.
     *
     * @param float $baseTotalRefunded
     * @return $this
     */
    public function setBaseTotalRefunded($baseTotalRefunded);

    /**
     * Sets the base tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseTaxAmount($amount);

    /**
     * Sets the discount amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setDiscountAmount($amount);

    /**
     * Sets the grand total for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setGrandTotal($amount);

    /**
     * Sets the shipping amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingAmount($amount);

    /**
     * Sets the shipping invoiced amount for the order.
     *
     * @param float $shippingInvoiced
     * @return $this
     */
    public function setShippingInvoiced($shippingInvoiced);

    /**
     * Sets the shipping refunded amount for the order.
     *
     * @param float $shippingRefunded
     * @return $this
     */
    public function setShippingRefunded($shippingRefunded);

    /**
     * Sets the shipping tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingTaxAmount($amount);

    /**
     * Sets the shipping tax refunded amount for the order.
     *
     * @param float $shippingTaxRefunded
     * @return $this
     */
    public function setShippingTaxRefunded($shippingTaxRefunded);

    /**
     * Sets the subtotal for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setSubtotal($amount);

    /**
     * Sets the tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setTaxAmount($amount);

    /**
     * Sets the total invoiced amount for the order.
     *
     * @param float $totalInvoiced
     * @return $this
     */
    public function setTotalInvoiced($totalInvoiced);

    /**
     * Sets the total paid for the order.
     *
     * @param float $totalPaid
     * @return $this
     */
    public function setTotalPaid($totalPaid);

    /**
     * Sets the total quantity ordered for the order.
     *
     * @param float $totalQtyOrdered
     * @return $this
     */
    public function setTotalQtyOrdered($totalQtyOrdered);

    /**
     * Sets the total amount refunded amount for the order.
     *
     * @param float $totalRefunded
     * @return $this
     */
    public function setTotalRefunded($totalRefunded);

    /**
     * Sets the tax refunded amount for the order.
     *
     * @param float $taxRefunded
     * @return $this
     */
    public function setTaxRefunded($taxRefunded);

    /**
     * Sets the base tax refunded amount for the order.
     *
     * @param float $baseTaxRefunded
     * @return $this
     */
    public function setBaseTaxRefunded($baseTaxRefunded);

    /**
     * Sets the tax invoiced amount for the order.
     *
     * @param float $taxInvoiced
     * @return $this
     */
    public function setTaxInvoiced($taxInvoiced);

    /**
     * Sets the base tax invoiced amount for the order.
     *
     * @param float $baseTaxInvoiced
     * @return $this
     */
    public function setBaseTaxInvoiced($baseTaxInvoiced);

    /**
     * Sets the tax canceled amount for the order.
     *
     * @param float $taxCanceled
     * @return $this
     */
    public function setTaxCanceled($taxCanceled);

    /**
     * Sets the base tax canceled for the order.
     *
     * @param float $baseTaxCanceled
     * @return $this
     */
    public function setBaseTaxCanceled($baseTaxCanceled);

    /**
     * Sets the subtotal including tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setSubtotalInclTax($amount);

    /**
     * Sets the base subtotal including tax for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseSubtotalInclTax($amount);

    /**
     * Sets the weight for the order.
     *
     * @param float $weight
     * @return $this
     */
    public function setWeight($weight);

    /**
     * Sets the shipping method for the order.
     *
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod);

    /**
     * Sets the created-at timestamp for the order.
     *
     * @param string $createdAt timestamp
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Sets the updated-at timestamp for the order.
     *
     * @param string $timestamp
     * @return $this
     */
    public function setUpdatedAt($timestamp);

    /**
     * Sets the shipping including tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingInclTax($amount);

    /**
     * Sets the base shipping including tax for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseShippingInclTax($amount);

    /**
     * Sets the total due for the order.
     *
     * @param float $totalDue
     * @return $this
     */
    public function setTotalDue($totalDue);

    /**
     * Sets the base total due for the order.
     *
     * @param float $baseTotalDue
     * @return $this
     */
    public function setBaseTotalDue($baseTotalDue);

    /**
     * Sets the bill to name for the order.
     *
     * @param string $billToName
     * @return $this
     */
    public function setBillingName($billToName);

    /**
     * Sets the shipping to name for the order.
     *
     * @param string $shippingToName
     * @return $this
     */
    public function setShippingName($shippingToName);

    /**
     * Sets order payment
     *
     * @param \Magento\Sales\Api\Data\OrderAddressInterface|null $address
     * @return \Magento\Sales\Api\Data\OrderAddressInterface
    */
    public function setBillingAddress(\Magento\Sales\Api\Data\OrderAddressInterface $address = null);

    /**
     * Declare order shipping address
     *
     * @param \Magento\Sales\Api\Data\OrderAddressInterface|null $address
     * @return $this
     */
    public function setShippingAddress(\Magento\Sales\Api\Data\OrderAddressInterface $address = null);

    /**
     * Sets the customer email for the order.
     *
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Sets the customer group for the order.
     *
     * @param string $customerGroup
     * @return $this
     */
    public function setCustomerGroup($customerGroup);

    /**
     * Sets the shipping and handling for the order.
     *
     * @param string $shippingAndHandling
     * @return $this
     */
    public function setShippingAndHandling($shippingAndHandling);

    /**
     * Sets the customer name for the order.
     *
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * Sets the payment method for the order.
     *
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod);
    
    /**
     * Sets base currency code
     *
     * @param string $baseCurrencyCode
     * @return $this
     */
    public function setBaseCurrencyCode($baseCurrencyCode);
    
    /**
     * Sets order currency code
     *
     * @param string $orderCurrencyCode
     * @return $this
     */
    public function setOrderCurrencyCode($orderCurrencyCode);
    
    /**
     * Set increment id
     * 
     * @param string $incrementId
     * @return $this
     */
    public function setIncrementId($incrementId);
}
