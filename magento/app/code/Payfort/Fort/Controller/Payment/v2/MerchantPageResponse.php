<?php

namespace Payfort\Fort\Controller\Payment;

class MerchantPageResponse extends \Payfort\Fort\Controller\Checkout
{
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('merchant_reference');
        $order = $this->getOrderById($orderId);
        $responseParams = $this->getRequest()->getParams();
        $helper = $this->getHelper();
        
        $paymentMethod  = $order->getPayment()->getMethod();
        if ($paymentMethod  == $helper::PAYFORT_FORT_PAYMENT_METHOD_INSTALLMENTS) {
            $integrationType = $helper->getConfig('payment/payfort_fort_installments/integration_type');
        }
        else {
            $integrationType = $helper->getConfig('payment/payfort_fort_cc/integration_type');
        }
        
        $success = $helper->handleFortResponse($responseParams, 'online', $integrationType);

        /*simicustomize*/
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customizeHelper = $objectManager->get('Simi\Simicustomize\Helper\Data');
        $storeBase = $customizeHelper->getStoreConfig('simiconnector/general/pwa_studio_url');
        if ($success) {
            $returnUrl = $storeBase.'thankyou.html';
        }
        else {
            $returnUrl = $storeBase.'cart.html?payment=false';
        }
        $this->orderRedirect($order, $returnUrl);
    }

}
