<?php


namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;

class PaymentMethodIsActive implements ObserverInterface {

    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        $this->simiObjectManager = $simiObjectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $method = $observer['method_instance'];
        $result = $observer->getEvent()->getResult();
        if (
            $this->simiObjectManager->get('Simi\Simicustomize\Helper\SpecialOrder')->isQuotePreOrder() ||
            $this->simiObjectManager->get('Simi\Simicustomize\Helper\SpecialOrder')->isQuoteTryToBuy()
        ) {
            if ($method->getCode() == 'cashondelivery') {
                $result->setData('is_available', false);
            } else if ($method->getCode() == 'banktransfer') {
                $result->setData('is_available', false);
            } else if ($method->getCode() == 'checkmo') {
                $result->setData('is_available', false);
            }
        }
        if ($method->getCode() == 'paypal_express_bml') {
            $result->setData('is_available', false);
        }
    }

}
