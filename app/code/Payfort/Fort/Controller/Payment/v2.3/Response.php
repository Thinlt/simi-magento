<?php

namespace Payfort\Fort\Controller\Payment;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Response extends \Payfort\Fort\Controller\Checkout implements CsrfAwareActionInterface, HttpGetActionInterface, HttpPostActionInterface
{
    
    
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
    
    public function execute()
    {
        $orderId            = $this->getRequest()->getParam('merchant_reference');
        $order              = $this->getOrderById($orderId);
        $responseParams     = $this->getRequest()->getParams();          
        $helper = $this->getHelper();  
        
        $integrationType    = $helper::PAYFORT_FORT_INTEGRATION_TYPE_REDIRECTION;
        $paymentMethod      = $order->getPayment()->getMethod();

        if($paymentMethod == $helper::PAYFORT_FORT_PAYMENT_METHOD_CC) {
            $integrationType = $helper->getConfig('payment/payfort_fort_cc/integration_type');
        }
        elseif($paymentMethod == $helper::PAYFORT_FORT_PAYMENT_METHOD_INSTALLMENTS) {
            $integrationType = $helper->getConfig('payment/payfort_fort_installments/integration_type');
        }
        
        $success = $helper->handleFortResponse($responseParams, 'offline', $integrationType);

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