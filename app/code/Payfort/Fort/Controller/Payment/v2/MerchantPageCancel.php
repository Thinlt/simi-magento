<?php

namespace Payfort\Fort\Controller\Payment;

class MerchantPageCancel extends \Payfort\Fort\Controller\Checkout
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