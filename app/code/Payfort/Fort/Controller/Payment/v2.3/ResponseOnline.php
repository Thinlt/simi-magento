<?php

namespace Payfort\Fort\Controller\Payment;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

class ResponseOnline extends \Payfort\Fort\Controller\Checkout implements CsrfAwareActionInterface, HttpGetActionInterface, HttpPostActionInterface 
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
        $orderId = $this->getRequest()->getParam('merchant_reference');
        $order = $this->getOrderById($orderId);
        $responseParams = $this->getRequest()->getParams();
        $helper = $this->getHelper();
        $integrationType = $helper::PAYFORT_FORT_INTEGRATION_TYPE_REDIRECTION;
        $success = $helper->handleFortResponse($responseParams, 'online', $integrationType);

	    /*simicustomize*/
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	    $existedTransaction = $objectManager
		    ->create('Simi\Simiconnector\Model\Appreport')
		    ->getCollection()
		    ->addFieldToFilter('order_id', $order->getId())
		    ->getFirstItem();
	    $customizeHelper = $objectManager->get('Simi\Simicustomize\Helper\Data');
	    $storeBase = $customizeHelper->getStoreConfig('pwa_studio_config/base_config/pwa_url');
	    if ($success) {
		    $returnUrl = $helper->getUrl('checkout/onepage/success');

		    /* simi customize*/
		    if($orderId && $existedTransaction && $existedTransaction->getId()) {
			    $returnUrl = $storeBase ? $storeBase.'/thankyou.html' : 'http://jumla-sa.com/thankyou.html';
		    }
	    }
	    else {
		    $returnUrl = $this->getHelper()->getUrl('checkout/cart');

		    /* simi customize*/
		    if($orderId && $existedTransaction && $existedTransaction->getId()) {
			    $returnUrl = $storeBase ? $storeBase.'/cart.html?payment=false' : 'http://jumla-sa.com/cart.html?payment=false';
		    }
	    }
        $this->orderRedirect($order, $returnUrl);
    }

}
