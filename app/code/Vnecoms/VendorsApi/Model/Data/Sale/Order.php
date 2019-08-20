<?php

namespace Vnecoms\VendorsApi\Model\Data\Sale;

/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Order extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface
{
    /**
     * Gets the ID for the order.
     *
     * @return int|null Order ID.
     */
    public function getEntityId(){
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Gets the Vendor ID for the order.
     *
     * @return int|null Order ID.
     */
    public function getVendorId(){
        return $this->_get(self::VENDOR_ID);
    }

    /**
     * Gets the order ID for the order.
     *
     * @return int|null External order ID.
     */
    public function getOrderId(){
        return $this->_get(self::ORDER_ID);
    }

    /**
     * Gets the status for the order.
     *
     * @return string|null Status.
     */
    public function getStatus(){
        return $this->_get(self::STATUS);
    }

    /**
     * Gets the shipping description for the order.
     *
     * @return string|null Shipping description.
     */
    public function getShippingDescription(){
        return $this->_get(self::SHIPPING_DESCRIPTION);
    }

    /**
     * Gets the base discount amount for the order.
     *
     * @return float|null Base discount amount.
     */
    public function getBaseDiscountAmount(){
        return $this->_get(self::BASE_DISCOUNT_AMOUNT);
    }

    /**
     * Gets the base grand total for the order.
     *
     * @return float Base grand total.
     */
    public function getBaseGrandTotal(){
        return $this->_get(self::BASE_GRAND_TOTAL);
    }

    /**
     * Gets the base shipping invoiced amount for the order.
     *
     * @return float|null Base shipping invoiced.
     */
    public function getBaseShippingInvoiced(){
        return $this->_get(self::BASE_SHIPPING_INVOICED);
    }

    /**
     * Gets the base shipping refunded amount for the order.
     *
     * @return float|null Base shipping refunded.
     */
    public function getBaseShippingRefunded(){
        return $this->_get(self::BASE_SHIPPING_REFUNDED);
    }

    /**
     * Gets the base shipping amount for the order.
     *
     * @return float|null Base shipping amount.
     */
    public function getBaseShippingAmount(){
        return $this->_get(self::BASE_SHIPPING_AMOUNT);
    }

    /**
     * Gets the base shipping tax amount for the order.
     *
     * @return float|null Base shipping tax amount.
     */
    public function getBaseShippingTaxAmount(){
        return $this->_get(self::BASE_SHIPPING_TAX_AMOUNT);
    }

    /**
     * Gets the base shipping tax refunded amount for the order.
     *
     * @return float|null Base shipping tax refunded.
     */
    public function getBaseShippingTaxRefunded(){
        return $this->_get(self::BASE_SHIPPING_TAX_REFUNDED);
    }

    /**
     * Gets the base subtotal for the order.
     *
     * @return float|null Base subtotal.
     */
    public function getBaseSubtotal(){
        return $this->_get(self::BASE_SUBTOTAL);
    }

    /**
     * Gets the base total invoiced amount for the order.
     *
     * @return float|null Base total invoiced.
     */
    public function getBaseTotalInvoiced(){
        return $this->_get(self::BASE_TOTAL_INVOICED);
    }

    /**
     * Gets the base total paid for the order.
     *
     * @return float|null Base total paid.
     */
    public function getBaseTotalPaid(){
        return $this->_get(self::BASE_TOTAL_PAID);
    }

    /**
     * Gets the base total refunded amount for the order.
     *
     * @return float|null Base total refunded.
     */
    public function getBaseTotalRefunded(){
        return $this->_get(self::BASE_TOTAL_REFUNDED);
    }

    /**
     * Gets the base tax amount for the order.
     *
     * @return float|null Base tax amount.
     */
    public function getBaseTaxAmount(){
        return $this->_get(self::BASE_TAX_AMOUNT);
    }

    /**
     * Gets the discount amount for the order.
     *
     * @return float|null Discount amount.
     */
    public function getDiscountAmount(){
        return $this->_get(self::DISCOUNT_AMOUNT);
    }

    /**
     * Gets the grand total for the order.
     *
     * @return float Grand total.
     */
    public function getGrandTotal(){
        return $this->_get(self::GRAND_TOTAL);
    }

    /**
     * Gets the shipping amount for the order.
     *
     * @return float|null Shipping amount.
     */
    public function getShippingAmount(){
        return $this->_get(self::SHIPPING_AMOUNT);
    }

    /**
     * Gets the shipping invoiced amount for the order.
     *
     * @return float|null Shipping invoiced amount.
     */
    public function getShippingInvoiced(){
        return $this->_get(self::SHIPPING_INVOICED);
    }

    /**
     * Gets the shipping refunded amount for the order.
     *
     * @return float|null Shipping refunded amount.
     */
    public function getShippingRefunded(){
        return $this->_get(self::SHIPPING_REFUNDED);
    }

    /**
     * Gets the shipping tax amount for the order.
     *
     * @return float|null Shipping tax amount.
     */
    public function getShippingTaxAmount(){
        return $this->_get(self::SHIPPING_TAX_AMOUNT);
    }

    /**
     * Gets the shipping tax refunded amount for the order.
     *
     * @return float|null Shipping tax refunded amount.
     */
    public function getShippingTaxRefunded(){
        return $this->_get(self::SHIPPING_TAX_REFUNDED);
    }

    /**
     * Gets the subtotal for the order.
     *
     * @return float|null Subtotal.
     */
    public function getSubtotal(){
        return $this->_get(self::SUBTOTAL);
    }

    /**
     * Gets the tax amount for the order.
     *
     * @return float|null Tax amount.
     */
    public function getTaxAmount(){
        return $this->_get(self::TAX_AMOUNT);
    }

    /**
     * Gets the total invoiced amount for the order.
     *
     * @return float|null Total invoiced amount.
     */
    public function getTotalInvoiced(){
        return $this->_get(self::TOTAL_INVOICED);
    }

    /**
     * Gets the total paid for the order.
     *
     * @return float|null Total paid.
     */
    public function getTotalPaid(){
        return $this->_get(self::TOTAL_PAID);
    }

    /**
     * Gets the total quantity ordered for the order.
     *
     * @return float|null Total quantity ordered.
     */
    public function getTotalQtyOrdered(){
        return $this->_get(self::TOTAL_QTY_ORDERED);
    }

    /**
     * Gets the total amount refunded amount for the order.
     *
     * @return float|null Total amount refunded.
     */
    public function getTotalRefunded(){
        return $this->_get(self::TOTAL_REFUNDED);
    }

    /**
     * Gets the tax refunded amount for the order.
     *
     * @return float|null Tax refunded amount.
     */
    public function getTaxRefunded(){
        return $this->_get(self::TAX_REFUNDED);
    }

    /**
     * Gets the base tax refunded amount for the order.
     *
     * @return float|null Base tax refunded.
     */
    public function getBaseTaxRefunded(){
        return $this->_get(self::BASE_TAX_REFUNDED);
    }

    /**
     * Gets the tax invoiced amount for the order.
     *
     * @return float|null Tax invoiced amount.
     */
    public function getTaxInvoiced(){
        return $this->_get(self::TAX_INVOICED);
    }

    /**
     * Gets the base tax invoiced amount for the order.
     *
     * @return float|null Base tax invoiced.
     */
    public function getBaseTaxInvoiced(){
        return $this->_get(self::BASE_TAX_INVOICED);
    }

    /**
     * Gets the tax canceled amount for the order.
     *
     * @return float|null Tax canceled amount.
     */
    public function getTaxCanceled(){
        return $this->_get(self::TAX_CANCELED);
    }

    /**
     * Gets the base tax canceled for the order.
     *
     * @return float|null Base tax canceled.
     */
    public function getBaseTaxCanceled(){
        return $this->_get(self::BASE_TAX_CANCELED);
    }

    /**
     * Gets the subtotal including tax amount for the order.
     *
     * @return float|null Subtotal including tax amount.
     */
    public function getSubtotalInclTax(){
        return $this->_get(self::SUBTOTAL_INCL_TAX);
    }

    /**
     * Gets the base subtotal including tax for the order.
     *
     * @return float|null Base subtotal including tax.
     */
    public function getBaseSubtotalInclTax(){
        return $this->_get(self::BASE_SUBTOTAL_INCL_TAX);
    }

    /**
     * Gets the weight for the order.
     *
     * @return float|null Weight.
     */
    public function getWeight(){
        return $this->_get(self::WEIGHT);
    }

    /**
     * Gets the shipping method for the order.
     *
     * @return string|null Base shipping amount.
     */
    public function getShippingMethod(){
        return $this->_get(self::SHIPPING_METHOD);
    }

    /**
     * Gets the created-at timestamp for the order.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt(){
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Gets the updated-at timestamp for the order.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt(){
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Gets the shipping including tax amount for the order.
     *
     * @return float|null Shipping including tax amount.
     */
    public function getShippingInclTax(){
        return $this->_get(self::SHIPPING_INCL_TAX);
    }

    /**
     * Gets the base shipping including tax for the order.
     *
     * @return float|null Base shipping including tax.
     */
    public function getBaseShippingInclTax(){
        return $this->_get(self::BASE_SHIPPING_INCL_TAX);
    }

    /**
     * Gets the total due for the order.
     *
     * @return float|null Total due.
     */
    public function getTotalDue(){
        return $this->_get(self::TOTAL_DUE);
    }

    /**
     * Gets the base total due for the order.
     *
     * @return float|null Base total due.
     */
    public function getBaseTotalDue(){
        return $this->_get(self::BASE_TOTAL_DUE);
    }

    /**
     * Gets the bill to name for the order.
     *
     * @return string|null Bill to name
     */
    public function getBillingName(){
        return $this->_get(self::BILL_TO_NAME);
    }

    /**
     * Gets the shipping to name for the order.
     *
     * @return string|null Shipping to name.
     */
    public function getShippingName(){
        return $this->_get(self::SHIPPING_TO_NAME);
    }

    /**
     * Retrieve order billing address
     *
     * @return \Magento\Sales\Api\Data\OrderAddressInterface|null
     */
    public function getBillingAddress(){
        return $this->_get(self::BILLING_ADDRESS);
    }

    /**
     * Retrieve order shipping address
     *
     * @return \Magento\Sales\Api\Data\OrderAddressInterface|null
     */
    public function getShippingAddress(){
        return $this->_get(self::SHIPPING_ADDRESS);
    }

    /**
     * Gets the customer email for the order.
     *
     * @return string|null Customer email.
     */
    public function getCustomerEmail(){
        return $this->_get(self::CUSTOMER_EMAIL);
    }

    /**
     * Gets the customer group for the order.
     *
     * @return string|null Customer group.
     */
    public function getCustomerGroup(){
        return $this->_get(self::CUSTOMER_GROUP);
    }

    /**
     * Gets the shipping and handling for the order.
     *
     * @return string|null Shipping and handling.
     */
    public function getShippingAndHandling(){
        return $this->_get(self::SHIPPING_AND_HANDLING);
    }

    /**
     * Gets the customer name for the order.
     *
     * @return string|null Customer name.
     */
    public function getCustomerName(){
        return $this->_get(self::CUSTOMER_NAME);
    }

    /**
     * Gets the payment method for the order.
     *
     * @return string|null Payment method.
     */
    public function getPaymentMethod(){
        return $this->_get(self::PAYMENT_METHOD);
    }

    
    /**
     * Get base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode(){
        return $this->_get(self::BASE_CURRENCY_CODE);
    }
    
    /**
     * Get order currency code
     *
     * @return string
    */
    public function getOrderCurrencyCode(){
        return $this->_get(self::ORDER_CURRENCY_CODE);
    }
    
    /**
     * Get Increment Id
     *
     * @return string
     */
    public function getIncrementId(){
        return $this->_get(self::INCREMENT_ID);
    }
    
    /**
     * Gets order payment
     *
     * @return \Magento\Sales\Api\Data\OrderPaymentInterface|null
     */
    public function getPayment(){
        return $this->_get(self::PAYMENT);
    }


    /**
     * Sets order payment
     *
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface|null $payment
     * @return \Magento\Sales\Api\Data\OrderPaymentInterface
     */
    public function setPayment(\Magento\Sales\Api\Data\OrderPaymentInterface $payment = null){
        return $this->setData(self::PAYMENT, $payment);
    }
    
    /**
     * Gets items for the order.
     *
     * @return \Magento\Sales\Api\Data\OrderItemInterface[] Array of items.
     */
    public function getItems(){
        return $this->_get(self::ITEMS);
    }
    
    /**
     * Set items for the order.
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface[] $items
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\OrderInterface
    */
    public function setItems($items){
        return $this->setData(self::ITEMS, $items);
    }
    

    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId){
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Sets the Vendor ID for the order.
     *
     * @param int $vendorId
     * @return $this
     */
    public function setVendorId($vendorId){
        return $this->setData(self::VENDOR_ID, $vendorId);
    }

    /**
     * Sets the order ID for the order.
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId){
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Sets the status for the order.
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Sets the shipping description for the order.
     *
     * @param string $description
     * @return $this
     */
    public function setShippingDescription($description){
        return $this->setData(self::SHIPPING_DESCRIPTION, $description);
    }

    /**
     * Sets the base discount amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseDiscountAmount($amount){
        return $this->setData(self::BASE_DISCOUNT_AMOUNT, $amount);
    }

    /**
     * Sets the base grand total for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseGrandTotal($amount){
        return $this->setData(self::BASE_GRAND_TOTAL, $amount);
    }

    /**
     * Sets the base shipping invoiced amount for the order.
     *
     * @param float $baseShippingInvoiced
     * @return $this
     */
    public function setBaseShippingInvoiced($baseShippingInvoiced){
        return $this->setData(self::BASE_SHIPPING_INVOICED, $baseShippingInvoiced);
    }

    /**
     * Sets the base shipping refunded amount for the order.
     *
     * @param float $baseShippingRefunded
     * @return $this
     */
    public function setBaseShippingRefunded($baseShippingRefunded){
        return $this->setData(self::BASE_SHIPPING_REFUNDED, $baseShippingRefunded);
    }

    /**
     * Sets the base shipping amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseShippingAmount($amount){
        return $this->setData(self::BASE_SHIPPING_AMOUNT, $amount);
    }

    /**
     * Sets the base shipping tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseShippingTaxAmount($amount){
        return $this->setData(self::BASE_SHIPPING_TAX_AMOUNT, $amount);
    }

    /**
     * Sets the base shipping tax refunded amount for the order.
     *
     * @param float $baseShippingTaxRefunded
     * @return $this
     */
    public function setBaseShippingTaxRefunded($baseShippingTaxRefunded){
        return $this->setData(self::BASE_SHIPPING_TAX_REFUNDED, $baseShippingTaxRefunded);
    }

    /**
     * Sets the base subtotal for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseSubtotal($amount){
        return $this->setData(self::BASE_SUBTOTAL, $amount);
    }

    /**
     * Sets the base total invoiced amount for the order.
     *
     * @param float $baseTotalInvoiced
     * @return $this
     */
    public function setBaseTotalInvoiced($baseTotalInvoiced){
        return $this->setData(self::BASE_TOTAL_INVOICED, $baseTotalInvoiced);
    }

    /**
     * Sets the base total paid for the order.
     *
     * @param float $baseTotalPaid
     * @return $this
     */
    public function setBaseTotalPaid($baseTotalPaid){
        return $this->setData(self::BASE_TOTAL_PAID, $baseTotalPaid);
    }

    /**
     * Sets the base total refunded amount for the order.
     *
     * @param float $baseTotalRefunded
     * @return $this
     */
    public function setBaseTotalRefunded($baseTotalRefunded){
        return $this->setData(self::BASE_TOTAL_REFUNDED, $baseTotalRefunded);
    }

    /**
     * Sets the base tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseTaxAmount($amount){
        return $this->setData(self::BASE_TAX_AMOUNT, $amount);
    }

    /**
     * Sets the discount amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setDiscountAmount($amount){
        return $this->setData(self::DISCOUNT_AMOUNT, $amount);
    }

    /**
     * Sets the grand total for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setGrandTotal($amount){
        return $this->setData(self::GRAND_TOTAL, $amount);
    }

    /**
     * Sets the shipping amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingAmount($amount){
        return $this->setData(self::SHIPPING_AMOUNT, $amount);
    }

    /**
     * Sets the shipping invoiced amount for the order.
     *
     * @param float $shippingInvoiced
     * @return $this
     */
    public function setShippingInvoiced($shippingInvoiced){
        return $this->setData(self::SHIPPING_INVOICED, $shippingInvoiced);
    }

    /**
     * Sets the shipping refunded amount for the order.
     *
     * @param float $shippingRefunded
     * @return $this
     */
    public function setShippingRefunded($shippingRefunded){
        return $this->setData(self::SHIPPING_REFUNDED, $shippingRefunded);
    }

    /**
     * Sets the shipping tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingTaxAmount($amount){
        return $this->setData(self::SHIPPING_TAX_AMOUNT, $amount);
    }

    /**
     * Sets the shipping tax refunded amount for the order.
     *
     * @param float $shippingTaxRefunded
     * @return $this
     */
    public function setShippingTaxRefunded($shippingTaxRefunded){
        return $this->setData(self::SHIPPING_TAX_REFUNDED, $shippingTaxRefunded);
    }

    /**
     * Sets the subtotal for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setSubtotal($amount){
        return $this->setData(self::SUBTOTAL, $amount);
    }

    /**
     * Sets the tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setTaxAmount($amount){
        return $this->setData(self::TAX_AMOUNT, $amount);
    }

    /**
     * Sets the total invoiced amount for the order.
     *
     * @param float $totalInvoiced
     * @return $this
     */
    public function setTotalInvoiced($totalInvoiced){
        return $this->setData(self::TOTAL_INVOICED, $totalInvoiced);
    }

    /**
     * Sets the total paid for the order.
     *
     * @param float $totalPaid
     * @return $this
     */
    public function setTotalPaid($totalPaid){
        return $this->setData(self::TOTAL_PAID, $totalPaid);
    }

    /**
     * Sets the total quantity ordered for the order.
     *
     * @param float $totalQtyOrdered
     * @return $this
     */
    public function setTotalQtyOrdered($totalQtyOrdered){
        return $this->setData(self::TOTAL_QTY_ORDERED, $totalQtyOrdered);
    }

    /**
     * Sets the total amount refunded amount for the order.
     *
     * @param float $totalRefunded
     * @return $this
     */
    public function setTotalRefunded($totalRefunded){
        return $this->setData(self::TOTAL_REFUNDED, $totalRefunded);
    }

    /**
     * Sets the tax refunded amount for the order.
     *
     * @param float $taxRefunded
     * @return $this
     */
    public function setTaxRefunded($taxRefunded){
        return $this->setData(self::TAX_REFUNDED, $taxRefunded);
    }

    /**
     * Sets the base tax refunded amount for the order.
     *
     * @param float $baseTaxRefunded
     * @return $this
     */
    public function setBaseTaxRefunded($baseTaxRefunded){
        return $this->setData(self::BASE_TAX_REFUNDED, $baseTaxRefunded);
    }

    /**
     * Sets the tax invoiced amount for the order.
     *
     * @param float $taxInvoiced
     * @return $this
     */
    public function setTaxInvoiced($taxInvoiced){
        return $this->setData(self::TAX_INVOICED, $taxInvoiced);
    }

    /**
     * Sets the base tax invoiced amount for the order.
     *
     * @param float $baseTaxInvoiced
     * @return $this
     */
    public function setBaseTaxInvoiced($baseTaxInvoiced){
        return $this->setData(self::BASE_TAX_INVOICED, $baseTaxInvoiced);
    }

    /**
     * Sets the tax canceled amount for the order.
     *
     * @param float $taxCanceled
     * @return $this
     */
    public function setTaxCanceled($taxCanceled){
        return $this->setData(self::TAX_CANCELED, $taxCanceled);
    }

    /**
     * Sets the base tax canceled for the order.
     *
     * @param float $baseTaxCanceled
     * @return $this
     */
    public function setBaseTaxCanceled($baseTaxCanceled){
        return $this->setData(self::BASE_TAX_CANCELED, $baseTaxCanceled);
    }

    /**
     * Sets the subtotal including tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setSubtotalInclTax($amount){
        return $this->setData(self::SUBTOTAL_INCL_TAX, $amount);
    }

    /**
     * Sets the base subtotal including tax for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseSubtotalInclTax($amount){
        return $this->setData(self::BASE_SUBTOTAL_INCL_TAX, $amount);
    }

    /**
     * Sets the weight for the order.
     *
     * @param float $weight
     * @return $this
     */
    public function setWeight($weight){
        return $this->setData(self::WEIGHT, $weight);
    }

    /**
     * Sets the shipping method for the order.
     *
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod){
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * Sets the created-at timestamp for the order.
     *
     * @param string $createdAt timestamp
     * @return $this
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Sets the updated-at timestamp for the order.
     *
     * @param string $timestamp
     * @return $this
     */
    public function setUpdatedAt($timestamp){
        return $this->setData(self::UPDATED_AT, $timestamp);
    }

    /**
     * Sets the shipping including tax amount for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingInclTax($amount){
        return $this->setData(self::SHIPPING_INCL_TAX, $amount);
    }

    /**
     * Sets the base shipping including tax for the order.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseShippingInclTax($amount){
        return $this->setData(self::BASE_SHIPPING_INCL_TAX, $amount);
    }

    /**
     * Sets the total due for the order.
     *
     * @param float $totalDue
     * @return $this
     */
    public function setTotalDue($totalDue){
        return $this->setData(self::TOTAL_DUE, $totalDue);
    }

    /**
     * Sets the base total due for the order.
     *
     * @param float $baseTotalDue
     * @return $this
     */
    public function setBaseTotalDue($baseTotalDue){
        return $this->setData(self::BASE_TOTAL_DUE, $baseTotalDue);
    }

    /**
     * Sets the bill to name for the order.
     *
     * @param string $billToName
     * @return $this
     */
    public function setBillingName($billToName){
        return $this->setData(self::BILL_TO_NAME, $billToName);
    }

    /**
     * Sets the shipping to name for the order.
     *
     * @param string $shippingToName
     * @return $this
     */
    public function setShippingName($shippingToName){
        return $this->setData(self::SHIPPING_TO_NAME, $shippingToName);
    }

    /**
     * Sets order payment
     *
     * @param \Magento\Sales\Api\Data\OrderAddressInterface|null $address
     * @return \Magento\Sales\Api\Data\OrderAddressInterface
    */
    public function setBillingAddress(\Magento\Sales\Api\Data\OrderAddressInterface $address = null){
        return $this->setData(self::BILLING_ADDRESS, $address);
    }

    /**
     * Declare order shipping address
     *
     * @param \Magento\Sales\Api\Data\OrderAddressInterface|null $address
     * @return $this
     */
    public function setShippingAddress(\Magento\Sales\Api\Data\OrderAddressInterface $address = null){
        return $this->setData(self::SHIPPING_ADDRESS, $address);
    }

    /**
     * Sets the customer email for the order.
     *
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail){
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * Sets the customer group for the order.
     *
     * @param string $customerGroup
     * @return $this
     */
    public function setCustomerGroup($customerGroup){
        return $this->setData(self::CUSTOMER_GROUP, $customerGroup);
    }

    /**
     * Sets the shipping and handling for the order.
     *
     * @param string $shippingAndHandling
     * @return $this
     */
    public function setShippingAndHandling($shippingAndHandling){
        return $this->setData(self::SHIPPING_AND_HANDLING, $shippingAndHandling);
    }

    /**
     * Sets the customer name for the order.
     *
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName){
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * Sets the payment method for the order.
     *
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod){
        return $this->setData(self::PAYMENT_METHOD, $paymentMethod);
    }
    
    /**
     * Sets base currency code
     *
     * @param string $baseCurrencyCode
     * @return $this
     */
    public function setBaseCurrencyCode($baseCurrencyCode){
        return $this->setData(self::BASE_CURRENCY_CODE, $baseCurrencyCode);
    }
    
    /**
     * Sets order currency code
     *
     * @param string $orderCurrencyCode
     * @return $this
    */
    public function setOrderCurrencyCode($orderCurrencyCode){
        return $this->setData(self::ORDER_CURRENCY_CODE, $orderCurrencyCode);
    }
    
    /**
     * Set increment id
     *
     * @param string $incrementId
     * @return $this
     */
    public function setIncrementId($incrementId){
        $this->setData(self::INCREMENT_ID, $incrementId);
    }
    
    /**
     * Gets can cancel
     *
     * @return bool
     */
    public function getCanCancel(){
        return $this->_get(self::CAN_CANCEL);
    }
    
    /**
     * Sets can cancel
     *
     * @param bool $canCancel
     * @return OrderInterface
    */
    public function setCanCancel($canCancel){
        $this->setData(self::CAN_CANCEL, $canCancel);
    }
    
    /**
     * Gets can invoice
     *
     * @return bool
    */
    public function getCanInvoice(){
        return $this->_get(self::CAN_INVOICE);
    }
    
    /**
     * Sets can invoice
     *
     * @param bool $canInvoice
     * @return OrderInterface
    */
    public function setCanInvoice($canInvoice){
        $this->setData(self::CAN_INVOICE, $canInvoice);
    }
    
    /**
     * Gets can ship
     *
     * @return bool
    */
    public function getCanShip(){
        return $this->_get(self::CAN_SHIP);
    }
    
    /**
     * Sets can Ship
     *
     * @param bool $canShip
     * @return OrderInterface
    */
    public function setCanShip($canShip){
        $this->setData(self::CAN_SHIP, $canShip);
    }
    
    /**
     * Gets can credit memo
     *
     * @return bool
    */
    public function getCanCreditMemo(){
        return $this->_get(self::CAN_CREDIT_MEMO);
    }
    
    /**
     * Sets can Credit memo
     *
     * @param bool
     * @return OrderInterface
    */
    public function setCanCreditMemo($canCreditMemo){
        $this->setData(self::CAN_CREDIT_MEMO, $canCreditMemo);
    }
}


