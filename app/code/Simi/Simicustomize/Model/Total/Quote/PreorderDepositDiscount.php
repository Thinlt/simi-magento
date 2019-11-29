<?php
namespace Simi\Simicustomize\Model\Total\Quote;

class PreorderDepositDiscount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    protected $_priceCurrency;
    public $simiObjectManager;
    protected $_checkOutSession;

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ){
        $this->setCode('preorder_deposit_discount');
        $this->simiObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_priceCurrency = $priceCurrency;
        $this->_checkOutSession = $this->simiObjectManager->create('Magento\Checkout\Model\Session');
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
        $quote->setPreorderDepositDiscount(0);
        if ($quote->getData('deposit_order_increment_id')) {
            $orderModel = $this->simiObjectManager->create('Magento\Sales\Model\Order')
                ->loadByIncrementId($quote->getData('deposit_order_increment_id'));
            if ($orderModel && $orderModel->getId()) {
                if ($this->validatePreOrderQuote($quote, $orderModel)) {
                    $baseDiscount = $orderModel->getData('base_grand_total');
                    $label = __('Pre-order Deposit Discount');
                    $discount = $this->_priceCurrency->convert($baseDiscount);
                    $total->addTotalAmount('preorder_deposit_discount', -$discount);
                    $total->addBaseTotalAmount('preorder_deposit_discount', -$baseDiscount);
                    if ($total->getBaseGrandTotal())
                        $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseDiscount);
                    if ($total->getGrandTotal())
                        $total->setGrandTotal($total->getGrandTotal() - $discount);
                    $total->setDiscountDescription($label);
                    $quote->setPreorderDepositDiscount(-$discount);
                }
            }
        }
        return $this;
    }

    public function validatePreOrderQuote($quote, $depositOrder) {
        if ($quote->getData('customer_email') &&
            $quote->getData('customer_email') !== $depositOrder->getData('customer_email'))
            return false;

        //compare preorder committed with quote
        $preOrderProducts = $this->simiObjectManager->get('\Simi\Simicustomize\Helper\SpecialOrder')
            ->getPreOrderProductsFromOrder($depositOrder);
        $quoteItems = $quote->getItemsCollection()->getData();
        $passed = true;
        if ($preOrderProducts && is_array($preOrderProducts)) {
            foreach ($preOrderProducts as $preOrderProduct) {
                foreach ($quoteItems as $quoteItem) {
                    if ($preOrderProduct['sku'] == $quoteItem['sku']) {
                        $passed = ((int)$preOrderProduct['quantity'] == (int)$quoteItem['qty']);
                    }
                }
            }
        }
        if (!$passed)
            return false;
        return true;
    }
}