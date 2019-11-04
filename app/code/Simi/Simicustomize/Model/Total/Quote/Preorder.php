<?php

namespace Simi\Simicustomize\Model\Total\Quote;

/**
* Class Custom
* @package Simi\Simicustomize\Model\Total\Quote
*/
class Preorder extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Pre-order deposit total label
     */
    const LABEL = 'Pre-order Remaining';

    /**
     * Deposit label
     */
    const LABEL_DEPOSIT = 'Pre-order Deposit';

    /**
     * Deposit label
     */
    const QUOTE_TYPE = 'pre_order';

    /**
    * @var \Magento\Framework\Pricing\PriceCurrencyInterface
    */
    protected $_priceCurrency;

    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $_config;

    /**
    * @var float
    */
    protected $_value;

    /**
    * @var bool
    */
    protected $_isDiscount = false;


    /**
     * Subtotal
     */
    protected $_baseSubtotal;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
    * Custom constructor.
    * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    */
    public function __construct(
       \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
       \Magento\Framework\App\Config\ScopeConfigInterface $config,
       \Magento\Checkout\Model\Session $checkoutSession,
       \Magento\Sales\Model\Order $order
    ){
        $this->setCode('preorder_deposit');
        $this->_priceCurrency = $priceCurrency;
        $this->_config = $config;
        $this->_checkoutSession = $checkoutSession;
        $this->order = $order;
    }
   /**
    * @param \Magento\Quote\Model\Quote $quote
    * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
    * @param \Magento\Quote\Model\Quote\Address\Total $total
    * @return $this|bool
    */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
        $address = $shippingAssignment->getShipping()->getAddress();
        /**
         * Check is pre-order item existed in quote
         */
        $items = $shippingAssignment->getItems();
        if ($this->_isPreorderAllowed($items)) {
            $baseDepositAmount = $this->_getDepositAmount($total);
            $depositAmount = $this->_priceCurrency->convert($baseDepositAmount);
            $baseDiscount = $this->_getRemaningAmount($total);
            $discount =  $this->_priceCurrency->convert($baseDiscount);
            // $total->addTotalAmount('preorder_deposit', -$discount);
            // $total->addBaseTotalAmount('preorder_deposit', -$baseDiscount);
            $total->addBaseTotalAmount('discount', -$baseDiscount); //set discount to payment
            $total->addTotalAmount('discount', -$discount); //set discount to payment
            // save data value to quote and address
            $quote->setQuoteType(self::QUOTE_TYPE);
            $quote->setDepositAmount($depositAmount);
            $quote->setBaseDepositAmount($baseDepositAmount);
            $quote->setRemainingAmount($discount);
            $quote->setBaseRemainingAmount($baseDiscount);
            $address->setDepositAmount($depositAmount);
            $address->setBaseDepositAmount($baseDepositAmount);
            $address->setRemainingAmount($discount);
            $address->setBaseRemainingAmount($baseDiscount);
            $address->setDiscountDescription(__('Pre-order'));
            $this->_isDiscount = true;
        } elseif ($quote->getReservedOrderId()) {
            // when pay for #2 order
            $order = $this->order->loadByIncrementId($quote->getReservedOrderId());
            if (!$order->getId() && $this->_checkoutSession->getLastDepositOrderId()) {
                $order = $this->order->loadByIncrementId($this->_checkoutSession->getLastDepositOrderId());
            }
            if ($order->getId() && $order->getRemainingAmount()) {
                $quote->setQuoteType(self::QUOTE_TYPE);
                $quote->setReservedOrderId($order->getIncrementId());
                $quote->setBaseRemainingAmount(0);
                $quote->setRemainingAmount(0);
                $quote->setBaseDepositAmount($order->getBaseDepositAmount());
                $quote->setDepositAmount($order->getDepositAmount());
                $address->setBaseDepositAmount($order->getBaseDepositAmount());
                $address->setDepositAmount($order->getDepositAmount());

                $discount = max($order->getSubtotal() - $order->getRemainingAmount(), $order->getDepositAmount());
                $baseDiscount = max($order->getBaseSubtotal() - $order->getBaseRemainingAmount(), $order->getBaseDepositAmount());
                $total->addTotalAmount('discount', -$discount); //set discount to payment when convert deposit to #2 order
                $total->addBaseTotalAmount('discount', -$baseDiscount); //set discount to payment when convert deposit to #2 order

                $total->setTotalAmount('shipping', 0);
                $total->setBaseTotalAmount('shipping', 0);
                $total->setTotalAmount('tax', 0);
                $total->setBaseTotalAmount('tax', 0);
                $total->setTaxAmount(0);

                $address->setShippingAmount(0);
                $address->setBaseShippingAmount(0);
                $address->setTaxAmount(0);
                $address->setBaseTaxAmount(0);
                $address->setFreeShipping(true);
                $address->setDiscountDescription(__('Pre-order'));
                $this->_isDiscount = false;
            }
        }
        return $this;
    }

    /**
     * Assign discount amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $items = $quote->getAllItems();
        if (!$this->_isPreorderAllowed($items)) {
            return [];
        }
        return [
            'code' => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => $this->_priceCurrency->convert($this->_getRemaningAmount($total))
        ];
    }

    /**
     * Get deposit amount base on baseSubtotal
     */
    protected function _getDepositAmount($total){
        if (!$this->_baseSubtotal) {
            $this->_baseSubtotal = $total->getBaseSubtotal();
        }
        $percent = max($this->_getPreorderValue(), 0); // percent
        $percent = min($percent, 100); // percent
        return ($this->_baseSubtotal / 100) * $percent;
    }

    /**
     * Get remaining amount base on baseSubtotal
     */
    protected function _getRemaningAmount($total){
        if (!$this->_baseSubtotal) {
            $this->_baseSubtotal = $total->getBaseSubtotal();
        }
        return $this->_baseSubtotal - $this->_getDepositAmount($total);
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __(self::LABEL);
    }

    protected function _getPreorderValue(){
        if (!$this->_value) {
            $depositAmount = $this->_config->getValue('sales/preorder/deposit_amount');
            if (is_numeric($depositAmount)) {
                $this->_value = (float) $depositAmount;
            } else {
                $this->_value = '';
            }
        }
        return $this->_value;
    }

    /**
     * Check preorder allowed if quote has pre-order items
     * return bool
     */
    protected function _isPreorderAllowed($items){
        if (!$this->_getPreorderValue()) {
            return false;
        }
        foreach ($items as $item) {
            $infoRequest = $item->getBuyRequest();
            if ($infoRequest && (int)$infoRequest->getData(self::QUOTE_TYPE)) {
                return true;
            }
        }
        return false;
    }
}