<?php

namespace Vnecoms\VendorsApi\Model\Data\Sale;

use Magento\Framework\Model\AbstractModel;

/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Invoice extends AbstractModel implements
    \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
{
    /**
     * Gets the Entity ID for the order.
     *
     * @return int|null Order ID.
     */
    public function getEntityId(){
        return $this->_getData(self::ENTITY_ID);
    }

    /**
     * Gets the Vendor ID for the invoice.
     *
     * @return int|null Order ID.
     */
    public function getVendorId(){
        return $this->_getData(self::VENDOR_ID);
    }

    /**
     * Gets the vendor order ID for the invoice.
     *
     * @return int|null External vendor order ID.
     */
    public function getVendorOrderId(){
        return $this->_getData(self::VENDOR_ORDER_ID);
    }

    /**
     * Gets the invoice ID for the invoice.
     *
     * @return int|null External invoice ID.
     */
    public function getInvoiceId(){
        return $this->_getData(self::INVOICE_ID);
    }

    /**
     * Gets the base grand total for the invoice.
     *
     * @return float Base grand total.
     */
    public function getBaseGrandTotal(){
        return $this->_getData(self::BASE_GRAND_TOTAL);
    }

    /**
     * Gets the shipping tax amount for the invoice.
     *
     * @return float|null Shipping tax amount.
     */
    public function getShippingTaxAmount(){
        return $this->_getData(self::SHIPPING_TAX_AMOUNT);
    }

    /**
     * Gets the tax amount for the invoice.
     *
     * @return float|null Tax amount.
     */
    public function getTaxAmount(){
        return $this->_getData(self::TAX_AMOUNT);
    }

    /**
     * Gets the base tax amount for the invoice.
     *
     * @return float|null Base tax amount.
     */
    public function getBaseTaxAmount(){
        return $this->_getData(self::BASE_TAX_AMOUNT);
    }

    /**
     * Gets the base shipping tax amount for the invoice.
     *
     * @return float|null Base shipping tax amount.
     */
    public function getBaseShippingTaxAmount(){
        return $this->_getData(self::BASE_SHIPPING_TAX_AMOUNT);
    }

    /**
     * Gets the base discount amount for the invoice.
     *
     * @return float|null Base discount amount.
     */
    public function getBaseDiscountAmount(){
        return $this->_getData(self::BASE_DISCOUNT_AMOUNT);
    }

    /**
     * Gets the grand total for the invoice.
     *
     * @return float Grand total.
     */
    public function getGrandTotal(){
        return $this->_getData(self::GRAND_TOTAL);
    }

    /**
     * Gets the shipping amount for the invoice.
     *
     * @return float|null Shipping amount.
     */
    public function getShippingAmount(){
        return $this->_getData(self::SHIPPING_AMOUNT);
    }

    /**
     * Gets the subtotal including tax amount for the invoice.
     *
     * @return float|null Subtotal including tax amount.
     */
    public function getSubtotalInclTax(){
        return $this->_getData(self::SUBTOTAL_INCL_TAX);
    }

    /**
     * Gets the base subtotal including tax for the invoice.
     *
     * @return float|null Base subtotal including tax.
     */
    public function getBaseSubtotalInclTax(){
        return $this->_getData(self::BASE_SUBTOTAL_INCL_TAX);
    }

    /**
     * Gets the base shipping amount for the invoice.
     *
     * @return float|null Base shipping amount.
     */
    public function getBaseShippingAmount(){
        return $this->_getData(self::BASE_SHIPPING_AMOUNT);
    }

    /**
     * Gets the total qty for the invoice.
     *
     * @return float|null Total qty.
     */
    public function getTotalQty(){
        return $this->_getData(self::TOTAL_QTY);
    }

    /**
     * Gets the subtotal for the invoice.
     *
     * @return float|null Subtotal.
     */
    public function getSubtotal(){
        return $this->_getData(self::SUBTOTAL);
    }

    /**
     * Gets the base subtotal for the invoice.
     *
     * @return float|null Base subtotal.
     */
    public function getBaseSubtotal(){
        return $this->_getData(self::BASE_SUBTOTAL);
    }

    /**
     * Gets the discount amount for the invoice.
     *
     * @return float|null Discount amount.
     */
    public function getDiscountAmount(){
        return $this->_getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * Gets the state for the invoice.
     *
     * @return string|null State.
     */
    public function getState(){
        return $this->_getData(self::STATE);
    }

    /**
     * Gets the created-at timestamp for the invoice.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Gets the updated-at timestamp for the invoice.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt(){
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * Gets the shipping including tax amount for the invoice.
     *
     * @return float|null Shipping including tax amount.
     */
    public function getShippingInclTax(){
        return $this->_getData(self::SHIPPING_INCL_TAX);
    }

    /**
     * Gets the base shipping including tax for the invoice.
     *
     * @return float|null Base shipping including tax.
     */
    public function getBaseShippingInclTax(){
        return $this->_getData(self::BASE_SHIPPING_INCL_TAX);
    }

    /**
     * Gets the base total refunded amount for the invoice.
     *
     * @return float|null Base total refunded.
     */
    public function getBaseTotalRefunded(){
        return $this->_getData(self::BASE_TOTAL_REFUNDED);
    }

    /**
     * Gets the discount description for the invoice.
     *
     * @return float|null Discount description.
     */
    public function getDiscountDescription(){
        return $this->_getData(self::DISCOUNT_DESCRIPTION);
    }

    /**
     * Gets the customer note for the invoice.
     *
     * @return string|null Customer note.
     */
    public function getCustomerNote(){
        return $this->_getData(self::CUSTOMER_NOTE);
    }





    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setEntityId($entityId){
        return $this->setData(self::ENTITY_ID, $entityId);
    }


    /**
     * Sets the Vendor ID for the invoice.
     *
     * @param int $vendorId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setVendorId($vendorId){
        return $this->setData(self::VENDOR_ID, $vendorId);
    }

    /**
     * Sets the vendor order ID for the invoice.
     *
     * @param int $vendorOrderId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setVendorOrderId($vendorOrderId){
        return $this->setData(self::VENDOR_ORDER_ID, $vendorOrderId);
    }

    /**
     * Sets the invoice ID for the invoice.
     *
     * @param int $invoiceId
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setInvoiceId($invoiceId){
        return $this->setData(self::INVOICE_ID, $invoiceId);
    }

    /**
     * Sets the base grand total for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseGrandTotal($amount){
        return $this->setData(self::BASE_GRAND_TOTAL, $amount);
    }

    /**
     * Sets the shipping tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setShippingTaxAmount($amount){
        return $this->setData(self::SHIPPING_TAX_AMOUNT, $amount);
    }

    /**
     * Sets the tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setTaxAmount($amount){
        return $this->setData(self::TAX_AMOUNT, $amount);
    }

    /**
     * Sets the base tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseTaxAmount($amount){
        return $this->setData(self::BASE_TAX_AMOUNT, $amount);
    }

    /**
     * Sets the base shipping tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseShippingTaxAmount($amount){
        return $this->setData(self::BASE_SHIPPING_TAX_AMOUNT, $amount);
    }

    /**
     * Sets the base discount amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseDiscountAmount($amount){
        return $this->setData(self::BASE_DISCOUNT_AMOUNT, $amount);
    }

    /**
     * Sets the grand total for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setGrandTotal($amount){
        return $this->setData(self::GRAND_TOTAL, $amount);
    }

    /**
     * Sets the shipping amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setShippingAmount($amount){
        return $this->setData(self::SHIPPING_AMOUNT, $amount);
    }

    /**
     * Sets the subtotal including tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setSubtotalInclTax($amount){
        return $this->setData(self::SUBTOTAL_INCL_TAX, $amount);
    }

    /**
     * Sets the base subtotal including tax for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseSubtotalInclTax($amount){
        return $this->setData(self::BASE_SUBTOTAL_INCL_TAX, $amount);
    }

    /**
     * Sets the base shipping amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseShippingAmount($amount){
        return $this->setData(self::BASE_SHIPPING_AMOUNT, $amount);
    }

    /**
     * Sets the total qty for the invoice.
     *
     * @param float $totalQty
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setTotalQty($totalQty){
        return $this->setData(self::TOTAL_QTY, $totalQty);
    }

    /**
     * Sets the subtotal for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setSubtotal($amount){
        return $this->setData(self::SUBTOTAL, $amount);
    }

    /**
     * Sets the base subtotal for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseSubtotal($amount){
        return $this->setData(self::BASE_SUBTOTAL, $amount);
    }

    /**
     * Sets the discount amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setDiscountAmount($amount){
        return $this->setData(self::DISCOUNT_AMOUNT, $amount);
    }

    /**
     * Sets the state for the invoice.
     *
     * @param string $state
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setState($state){
        return $this->setData(self::STATE, $state);
    }

    /**
     * Sets the created-at timestamp for the invoice.
     *
     * @param string $createdAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Sets the updated-at timestamp for the invoice.
     *
     * @param string $timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setUpdatedAt($timestamp){
        return $this->setData(self::UPDATED_AT, $timestamp);
    }

    /**
     * Sets the shipping including tax amount for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setShippingInclTax($amount){
        return $this->setData(self::SHIPPING_INCL_TAX, $amount);
    }

    /**
     * Sets the base shipping including tax for the invoice.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseShippingInclTax($amount){
        return $this->setData(self::BASE_SHIPPING_INCL_TAX, $amount);
    }

    /**
     * Sets the base total refunded amount for the invoice.
     *
     * @param float $baseTotalRefunded
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setBaseTotalRefunded($baseTotalRefunded){
        return $this->setData(self::BASE_TOTAL_REFUNDED, $baseTotalRefunded);
    }

    /**
     * Sets the discount description for the invoice.
     *
     * @param float $discountDescription
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setDiscountDescription($discountDescription){
        return $this->setData(self::DISCOUNT_DESCRIPTION, $discountDescription);
    }

    /**
     * Sets the customer note for the invoice.
     *
     * @param string $customerNote
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\InvoiceInterface
     */
    public function setCustomerNote($customerNote){
        return $this->setData(self::CUSTOMER_NOTE, $customerNote);
    }

}


