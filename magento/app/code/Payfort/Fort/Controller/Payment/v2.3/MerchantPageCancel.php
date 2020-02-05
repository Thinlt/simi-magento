<?php

namespace Payfort\Fort\Controller\Payment;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

class MerchantPageCancel extends \Payfort\Fort\Controller\Checkout implements CsrfAwareActionInterface, HttpGetActionInterface, HttpPostActionInterface
{
    public function execute()
    {
        $this->_cancelCurrenctOrderPayment('User has cancel the payment');
        $this->_checkoutSession->restoreQuote();
        
        $message = __('You have canceled the payment.');
        $this->messageManager->addError( $message );

        /*simicustomize*/
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customizeHelper = $objectManager->get('Simi\Simicustomize\Helper\Data');
        $storeBase = $customizeHelper->getStoreConfig('simiconnector/general/pwa_studio_url');
        $returnUrl = $storeBase.'cart.html?payment=false';
        $this->getResponse()->setRedirect($returnUrl);
    }
}

?>