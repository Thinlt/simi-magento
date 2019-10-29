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
    const LABEL = 'Pre-order Deposit';

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
    * Custom constructor.
    * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    */
    public function __construct(
       \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
       \Magento\Framework\App\Config\ScopeConfigInterface $config,
       \Magento\Sales\Model\Order $order
    ){
        $this->_priceCurrency = $priceCurrency;
        $this->_config = $config;
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
            $baseDiscount = $this->_getDepositAmount($total);
            $discount =  $this->_priceCurrency->convert($baseDiscount);
            // $total->addTotalAmount('preorder_deposit', -$discount);
            // $total->addBaseTotalAmount('preorder_deposit', -$baseDiscount);
            $total->addTotalAmount('discount', -$discount); //set discount to payment
            $total->addBaseTotalAmount('discount', -$baseDiscount); //set discount to payment
            // save data value to quote and address
            $quote->setDepositAmount($discount);
            $quote->setBaseDepositAmount($baseDiscount);
            $address->setDepositAmount($discount);
            $address->setBaseDepositAmount($baseDiscount);
            $address->setDiscountDescription(__('Pre-order Deposit'));
            $this->_isDiscount = true;
        } elseif ($quote->getReservedOrderId()) {
            $order = $this->order->loadByIncrementId($quote->getReservedOrderId());
            if ($order->getId() && $order->getDepositAmount()) {
                $orderTotals = max($order->getSubtotal() - $order->getDepositAmount(), $order->getTotalPaid());
                $orderBaseTotals = max($order->getBaseSubtotal() - $order->getBaseDepositAmount(), $order->getBaseTotalPaid());

                $discount = $orderTotals;
                $baseDiscount = $orderBaseTotals;
                
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
            'value' => $this->_priceCurrency->convert($this->_getDepositAmount($total))
        ];
    }

    protected function _getDepositAmount($total){
        if (!$this->_baseSubtotal) {
            $this->_baseSubtotal = $total->getBaseSubtotal();
        }
        $baseSubTotal = $this->_baseSubtotal;
        $percent = $this->_getPreorderValue(); // percent
        if ($percent > 100) {
            $percent = 100.00;
        }
        return $baseSubTotal - ($baseSubTotal / 100) * $percent;
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
     * Check preorder allowed
     * return bool
     */
    protected function _isPreorderAllowed($items){
        if (!$this->_getPreorderValue()) {
            return false;
        }
        foreach ($items as $item) {
            $infoRequest = $item->getBuyRequest();
            if ($infoRequest && (int)$infoRequest->getData('pre_order')) {
                return true;
            }
        }
        return false;
    }
}