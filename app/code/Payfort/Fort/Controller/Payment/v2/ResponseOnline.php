<?php

namespace Payfort\Fort\Controller\Payment;

class ResponseOnline extends \Payfort\Fort\Controller\Checkout
{
    
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('merchant_reference');
        $order = $this->getOrderById($orderId);
        $responseParams = $this->getRequest()->getParams();
        $helper = $this->getHelper();
        $integrationType = $helper::PAYFORT_FORT_INTEGRATION_TYPE_REDIRECTION;
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
