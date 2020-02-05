<?php

namespace Vnecoms\VendorsApi\Api\Data\Sale;

/**
 * Vendor Invoice interface.
 *
 * @api
 * @since 100.0.2
 */
interface InvoiceInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    /*
     * Entity ID.
     */
    const ENTITY_ID = 'entity_id';
    /*
     * Vendor ID.
     */
    const VENDOR_ID = 'vendor_id';
    /*
     * Vendor order ID.
     */
    const VENDOR_ORDER_ID = 'vendor_order_id';
    /*
     * Invoice ID.
     */
    const INVOICE_ID = 'invoice_id';
    /*
     * Base grand total.
     */
    const BASE_GRAND_TOTAL = 'base_grand_total';
    /*
     * Shipping tax amount.
     */
    const SHIPPING_TAX_AMOUNT = 'shipping_tax_amount';
    /*
     * Tax amount.
     */
    const TAX_AMOUNT = 'tax_amount';
    /*
     * Base tax amount.
     */
    const BASE_TAX_AMOUNT = 'base_tax_amount';
    /*
     * Base shipping tax amount.
     */
    const BASE_SHIPPING_TAX_AMOUNT = 'base_shipping_tax_amount';
    /*
     * Base discount amount.
     */
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
    /*
     * Grand total.
     */
    const GRAND_TOTAL = 'grand_total';
    /*
     * Shipping amount.
     */
    const SHIPPING_AMOUNT = 'shipping_amount';
    /*
     * Subtotal including tax.
     */
    const SUBTOTAL_INCL_TAX = 'subtotal_incl_tax';
    /*
     * Base subtotal including tax.
     */
    const BASE_SUBTOTAL_INCL_TAX = 'base_subtotal_incl_tax';
    /*
     * Base shipping amount.
     */
    const BASE_SHIPPING_AMOUNT = 'base_shipping_amount';
    /*
     * Total qty.
     */
    const TOTAL_QTY = 'total_qty';
    /*
     * Subtotal.
     */
    const SUBTOTAL = 'subtotal';
    /*
     * Base subtotal.
     */
    const BASE_SUBTOTAL = 'base_subtotal';
    /*
     * Discount amount.
     */
    const DISCOUNT_AMOUNT = 'discount_amount';
    /*
     * State.
     */
    const STATE = 'state';
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
     * Base total refunded.
     */
    const BASE_TOTAL_REFUNDED = 'base_total_refunded';
    /*
     * Discount description.
     */
    const DISCOUNT_DESCRIPTION = 'discount_description';
    /*
     * Customer note.
     */
    const CUSTOMER_NOTE = 'customer_note';


    /**
     * Gets the ID for the invoice.
     *
     * @return int|null Entity ID.
     */
    public function getEntityId();

    /**
     * Gets the Vendor ID for the invoice.
     *
     * @return int|null Vendor ID.
     */
    public function getVendorId();

    /**
     * Gets the vendor order ID for the invoice.
     *
     * @return int|null External vendor order ID.
     */
    public function getVendorOrderId();

    /**
     * Gets the invoice ID for the invoice.
     *
     * @return int|null External invoice ID.
     */
    public function getInvoiceId();

    /**
     * Gets the base grand total for the invoice.
     *
     * @return float Base grand total.
     */
    public function getBaseGrandTotal();

    /**
     * Gets the shipping tax amount for the invoice.
     *
     * @return float|null Shipping tax amount.
     */
    public function getShippingTaxAmount();

    /**
     * Gets the tax amount for the invoice.
     *
     * @return float|null Tax amount.
     */
    public function getTaxAmount();

    /**
     * Gets the base tax amount for the invoice.
     *
     * @return float|null Base tax amount.
     */
    public function getBaseTaxAmount();

    /**
     * Gets the base shipping tax amount for the invoice.
     *
     * @return float|null Base shipping tax amount.
     */
    public function getBaseShippingTaxAmount();

    /**
     * Gets the base discount amount for the invoice.
     *
     * @return float|null Base discount amount.
     */
    public function getBaseDiscountAmount();

    /**
     * Gets the grand total for the invoice.
     *
     * @return float Grand total.
     */
    public function getGrandTotal();

    /**
     * Gets the shipping amount for the invoice.
     *
     * @return float|null Shipping amount.
     */
    public function getShippingAmount();

    /**
     * Gets the subtotal including tax amount for the invoice.
     *
     * @return float|null Subtotal including tax amount.
     */
    public function getSubtotalInclTax();

    /**
     * Gets the base subtotal including tax for the invoice.
     *
     * @return float|null Base subtotal including tax.
     */
    public function getBaseSubtotalInclTax();

    /**
     * Gets the base shipping amount for the invoice.
     *
     * @return float|null Base shipping amount.
     */
    public function getBaseShippingAmount();

    /**
     * Gets the total qty for the invoice.
     *
     * @return float|null Total qty.
     */
    public function getTotalQty();

    /**
     * Gets the subtotal for the invoice.
     *
     * @return float|null Subtotal.
     */
    public function getSubtotal();

    /**
     * Gets the base subtotal for the invoice.
     *
     * @return float|null Base subtotal.
     */
    public function getBaseSubtotal();

    /**
     * Gets the discount amount for the invoice.
     *
     * @return float|null Discount amount.
     */
    public function getDiscountAmount();

    /**
     * Gets the state for the invoice.
     *
     * @return string|null State.
     */
    public function getState();

    /**
     * Gets the created-at timestamp for the invoice.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Gets the updated-at timestamp for the invoice.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt();

    /**
     * Gets the shipping including tax amount for the invoice.
     *
     * @return float|null Shipping including tax amount.
     */
    public function getShippingInclTax();

    /**
     * Gets the base shipping including tax for the invoice.
     *
     * @return float|null Base shipping including tax.
     */
    public function getBaseShippingInclTax();

    /**
     * Gets the base total refunded amount for the invoice.
     *
     * @return float|null Base total refunded.
     */
    public function getBaseTotalRefunded();

    /**
     * Gets the discount description for the invoice.
     *
     * @return float|null Discount description.
     */
    public function getDiscountDescription();

    /**
     * Gets the customer note for the invoice.
     *
     * @return string|null Customer note.
     */
    public function getCustomerNote();




    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setEntityId($entityId);

    /**
     * Sets the Vendor ID for the invoice.
     *
     * @param int $vendorId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setVendorId($vendorId);

    /**
     * Sets the vendor order ID for the invoice.
     *
     * @param int $vendorOrderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setVendorOrderId($vendorOrderId);

    /**
     * Sets the invoice ID for the invoice.
     *
     * @param int $invoiceId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setInvoiceId($invoiceId);

    /**
     * Sets the base grand total for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseGrandTotal($amount);

    /**
     * Sets the shipping tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setShippingTaxAmount($amount);

    /**
     * Sets the tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setTaxAmount($amount);

    /**
     * Sets the base tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseTaxAmount($amount);

    /**
     * Sets the base shipping tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseShippingTaxAmount($amount);

    /**
     * Sets the base discount amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseDiscountAmount($amount);

    /**
     * Sets the grand total for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setGrandTotal($amount);

    /**
     * Sets the shipping amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setShippingAmount($amount);

    /**
     * Sets the subtotal including tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setSubtotalInclTax($amount);

    /**
     * Sets the base subtotal including tax for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseSubtotalInclTax($amount);

    /**
     * Sets the base shipping amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseShippingAmount($amount);

    /**
     * Sets the total qty for the invoice.
     *
     * @param float $totalQty
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setTotalQty($totalQty);

    /**
     * Sets the subtotal for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setSubtotal($amount);

    /**
     * Sets the base subtotal for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseSubtotal($amount);

    /**
     * Sets the discount amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setDiscountAmount($amount);

    /**
     * Sets the state for the invoice.
     *
     * @param string $state
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setState($state);

    /**
     * Sets the created-at timestamp for the invoice.
     *
     * @param string $createdAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Sets the updated-at timestamp for the invoice.
     *
     * @param string $timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setUpdatedAt($timestamp);

    /**
     * Sets the shipping including tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setShippingInclTax($amount);

    /**
     * Sets the base shipping including tax for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseShippingInclTax($amount);

    /**
     * Sets the base total refunded amount for the invoice.
     *
     * @param float $baseTotalRefunded
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseTotalRefunded($baseTotalRefunded);

    /**
     * Sets the discount description for the invoice.
     *
     * @param float $discountDescription
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setDiscountDescription($discountDescription);

    /**
     * Sets the customer note for the invoice.
     *
     * @param string $customerNote
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setCustomerNote($customerNote);

}
