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
