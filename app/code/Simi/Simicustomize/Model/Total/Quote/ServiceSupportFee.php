<?php
namespace Simi\Simicustomize\Model\Total\Quote;

class ServiceSupportFee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
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
        $this->scopeConfig = $this->simiObjectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
        $quote->setServiceSupportFee(0);

        $baseServiceSupportFee = $this->scopeConfig->getValue('sales/service_support/service_support_fee');
        if ($baseServiceSupportFee) {
            $quoteItems = $quote->getItemsCollection()->getData();
            $applyFee = false;
            foreach ($quoteItems as $quoteItem) {
                if ($quoteItem['is_buy_service']) {
                    $applyFee = true;
                    break;
                }
            }
            if ($applyFee) {
                $serviceSupportFee = $this->_priceCurrency->convert($baseServiceSupportFee);
                $total->addTotalAmount('service_support_fee', $serviceSupportFee);
                $total->addBaseTotalAmount('service_support_fee', $baseServiceSupportFee);
                if ($total->getBaseGrandTotal())
                    $total->setBaseGrandTotal($total->getBaseGrandTotal() + $baseServiceSupportFee);
                if ($total->getGrandTotal())
                    $total->setGrandTotal($total->getGrandTotal() + $serviceSupportFee);
                $quote->setServiceSupportFee($serviceSupportFee);
            }
        }
        return $this;
    }

}