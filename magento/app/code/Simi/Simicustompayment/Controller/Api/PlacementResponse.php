<?php

namespace Simi\Simicustompayment\Controller\Api;

class PlacementResponse extends \Payfort\Fort\Controller\Checkout
{
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('merchant_reference');
        $order = $this->getOrderById($orderId);
        $responseParams = $this->getRequest()->getParams();
        $helper = $this->getHelper();
        $integrationType = $helper::PAYFORT_FORT_INTEGRATION_TYPE_REDIRECTION;
        $success = $helper->handleFortResponse($responseParams, 'online', $integrationType);
        if ($success) {
            $returnUrl = $helper->getUrl('pwa_sandbox/checkout/onepage/success');
        }
        else {
            $returnUrl = $this->getHelper()->getUrl('pwa_sandbox/checkout/cart');
        }
        $this->orderRedirect($order, $returnUrl);
    }

}
