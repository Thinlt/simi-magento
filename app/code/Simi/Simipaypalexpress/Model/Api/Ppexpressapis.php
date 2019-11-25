<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Simipaypalexpress\Model\Api;

class Ppexpressapis extends \Simi\Simiconnector\Model\Api\Apiabstract
{

    public $configType = 'Magento\Paypal\Model\Config';
    public $configMethod = \Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS;
    public $checkoutType = 'Magento\Paypal\Model\Express\Checkout';
    public $paramMobile = "&useraction=commit";
    public $checkout = null;
    public $config = null;
    public $quote = false;

    public function setBuilderQuery() {
        $this->config = $this->simiObjectManager->create($this->configType, array($this->configMethod));
        $this->config->setMethod($this->configMethod);
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            
        }
    }

    /*
     * Get Paypal API information
     */

    public function show() {
        $data = $this->getData();
        $result = array();
        if (isset($data['resourceid'])) {
            if ($data['resourceid'] == 'start') {
                $result['ppexpressapi'] = $this->startPayment();
            } else if ($data['resourceid'] == 'return') {
                $this->_initCheckout();
                $this->checkout->returnFromPaypal($this->_initToken());
                $result['ppexpressapi'] = array();
//                for pwa
//                $controller = $data['controller'];
//                $controller->getResponse()->setRedirect('/checkout/onepage?placeOrder=paypal');
            } else if ($data['resourceid'] == 'checkout_address') {
                $this->_initCheckout();
                $this->checkout->returnFromPaypal($this->_initToken());
                $this->checkout->prepareOrderReview($this->_initToken());
                $expressModel = $this->simiObjectManager->create("Simi\Simipaypalexpress\Model\simiconnectorexpress");
                $expressModel->setQuote($this->_getQuote());
                $addressSelected = $expressModel->getBillingShippingAddress();
                $result['ppexpressapi'] = $addressSelected['data'][0];
            } else if ($data['resourceid'] == 'shipping_methods') {
                $this->_initCheckout();
                $this->checkout->returnFromPaypal($this->_initToken());
                $this->checkout->prepareOrderReview($this->_initToken());
                $info['methods'] = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Checkout\Shipping')
                    ->getMethods();
                $result['ppexpressapi'] = $info;
            }
            else if($data['resourceid'] == 'placeOrder'){
                $this->_initCheckout();
                $this->checkout->returnFromPaypal($this->_initToken());
                // $controller = $data['controller'];
                // $controller->getResponse()->setRedirect('/paypal/express/review/');
                $result = array();
                $session = $this->_getCheckoutSession();
                $this->checkout->place($this->_initToken());
                $order = $this->checkout->getOrder();
                $session->clearHelperData();
                if($order && $order->getIncrementId()){
                    $orderId = $order->getIncrementId();
                    $result['order'] = ['invoice_number' => $orderId];
                    $this->cleanCheckoutSession();
                }
            }
            else if ($data['resourceid'] == 'cancel') {
                # code...
                $controller = $data['controller'];
                $controller->getResponse()->setRedirect('/checkout/cart');
            } else if (strpos($data['resourceid'], 'return') !== false) { //for pwa-studio
                $controller = $data['controller'];
                $quoteId = explode('_', $data['resourceid']);
                $quoteId = $quoteId[1];
                $quoteModel = $this->simiObjectManager->get('Magento\Quote\Model\Quote')->load($quoteId);
                if ($quoteModel->getId() && $quoteModel->getData('is_active')) {
                    $this->quote = $quoteModel;
                    $this->_initCheckout();
                    $this->checkout->returnFromPaypal($this->_initToken());
                    if ($success_callback = $this->getStoreConfig('simipaypalexpress/pwa_studio/success_callback')) {
                        $controller->getResponse()->setRedirect($success_callback . '&token=' . $controller->getRequest()->getParam('token'));
                    }
                }
            }  else if (strpos($data['resourceid'], 'cancel')  !== false) { //for pwa-studio
                $controller = $data['controller'];
                $quoteId = explode('_', $data['resourceid']);
                $quoteId = $quoteId[1];
                if ($success_callback = $this->getStoreConfig('simipaypalexpress/pwa_studio/failure_callback')) {
                    $controller->getResponse()->setRedirect($success_callback);
                }
            }
        }
        return $result;
    }

    /*
     * Update paypal checkout information
     */

    public function update() {
        $data = $this->getData();
        $result = array();
        $parameters = (array) $data['contents'];
        if (isset($data['resourceid'])) {
            if ($data['resourceid'] == 'checkout_address') {
                $expressModel = $this->simiObjectManager->create("Simi\Simipaypalexpress\Model\simiconnectorexpress");
                $expressModel->setQuote($this->_getQuote());
                $info = $expressModel->updateAddress($parameters);
                $this->_initCheckout();
                $this->checkout->returnFromPaypal($this->_initToken());
                $this->checkout->prepareOrderReview($this->_initToken());
                $addressSelected = $expressModel->getBillingShippingAddress();
                $result['ppexpressapi'] = $addressSelected['data'];
            }
        }
        return $result;
    }

    /*
     * Place Order
     */

    public function store() {
        $data = $this->getData();
        $result = array();
        $order = array();
        $parameters = (array) $data['contents'];
        if (!isset($parameters['s_method'])) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Please select Shipping Method'), 6);
        } if (isset($data['resourceid'])) {
            if ($data['resourceid'] == 'place') {
                $this->_initCheckout();
                $this->checkout->place($this->_initToken(), $parameters['s_method']->method);
                $session = $this->_getCheckoutSession();
                $session->clearHelperData();
                $order['message'] = __("Thank you for your purchase!");
            }
        }
        $result['order'] = $order;
        $this->cleanCheckoutSession();
        return $result;
    }

    public function cleanCheckoutSession()
    {
        try {
            $quote = $this->_getQuote();
            $quote->setIsActive(false);
            $quote->delete();
        } catch (\Exception $e) {
            $this->_getCheckoutSession()->clearQuote()->clearStorage();
        }
        $checkoutSession = $this->_getCheckoutSession();
        $checkoutSession->clearQuote();
        $checkoutSession->clearStorage();
        $checkoutSession->clearHelperData();
        $checkoutSession->resetCheckout();
        $checkoutSession->restoreQuote();
    }

    
    /*
     * get Paypal start payment information
     */

    public function startPayment() {
        $data = $this->getData();
        $controller = $data['controller'];

        $quote = $this->_getQuote();

        try { //when quote total dismatch the ordertotal, paypal would throw error, need to update quote address again
            if ($quote->isMultipleShippingAddresses()) {
                foreach ($quote->getAllShippingAddresses() as $address) {
                    $quote->removeAddress($address->getId());
                }

                $shippingAddress = $quote->getShippingAddress();
                $defaultShipping = $quote->getCustomer()->getDefaultShipping();
                if ($defaultShipping) {
                    $defaultCustomerAddress = $this->simiObjectManager->create('\Magento\Customer\Api\AddressRepositoryInterface')->getById(
                        $defaultShipping
                    );
                    $shippingAddress->importCustomerAddressData($defaultCustomerAddress);
                }
                $this->simiObjectManager->create('\Magento\Quote\Api\CartRepositoryInterface')->save($quote);
            }
        } catch (\Exception $e) {}



        if ((int) $this->getStoreConfig('simipaypalexpress/general/enable_app') == 0) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('PayPal Express was disabled in App'), 6);
        }
        $this->_initCheckout();

        if ($this->_getQuote()->getIsMultiShipping()) {
            $this->_getQuote()->setIsMultiShipping(false);
            $this->_getQuote()->removeAllAddresses();
        }

        $customer = $this->simiObjectManager->get('Magento\Customer\Model\Session')->getCustomer();
        if ($customer && $customer->getId()) {
            $customerData = $this->simiObjectManager->get('Magento\Customer\Model\Session')->getCustomerDataObject();
            $this->checkout->setCustomerWithAddressChange(
                    $customerData,
                    $this->_getQuote()->getBillingAddress(),
                    $this->_getQuote()->getShippingAddress()
            );
        }

        // billing agreement
        $isBARequested = (bool) $controller->getRequest()
                        ->getParam(\Magento\Paypal\Model\Express\Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
        if ($customer && $customer->getId()) {
            $this->checkout->setIsBillingAgreementRequested($isBARequested);
        }
        
        // giropay
        $this->checkout->prepareGiropayUrls($this->simiObjectManager->get('Magento\Framework\UrlInterface')
                    ->getUrl('checkout/onepage/success'),
                $this->simiObjectManager->get('Magento\Framework\UrlInterface')
                    ->getUrl('paypal/express/cancel'),
                $this->simiObjectManager->get('Magento\Framework\UrlInterface')
                    ->getUrl('checkout/onepage/success')
        );

        $returnresourceid = 'return';
        if ($controller->getRequest()->getParam('quote_id')) { //pwa-studio
            $returnresourceid .= ('_'.$this->_getQuote()->getId());
        }
        $cancelresourceid = 'cancel';
        if ($controller->getRequest()->getParam('quote_id')) { //pwa-studio
            $cancelresourceid .= ('_'.$this->_getQuote()->getId());
        }
        $token = $this->checkout->start($this->simiObjectManager->get('Magento\Framework\UrlInterface')
                    ->getUrl('simiconnector/rest/v2/ppexpressapis/'.$returnresourceid),
                $this->simiObjectManager->get('Magento\Framework\UrlInterface')
                    ->getUrl('simiconnector/rest/v2/ppexpressapis/'.$cancelresourceid));

        $review_address = $this->getStoreConfig('simipaypalexpress/general/enable');
        $this->_initToken($token);
        $url = $this->checkout->getRedirectUrl();
        $url .= $this->paramMobile;

        return array("url" => $url,
                "review_address" => $review_address,
        );
    }

    protected function _getQuote() {
        if (!$this->quote) {
            $this->quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->quote;
    }

    private function _getCheckoutSession() {
        return $this->simiObjectManager->get('\Magento\Checkout\Model\Session');
    }

    protected function _initCheckout() {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Unable to initialize Express Checkout.'), 4);
        }
        $this->checkout =  $this->simiObjectManager->create(
            $this->checkoutType,
            [
                'params' => [
                    'config' => $this->config,
                    'quote' => $quote,
                ]
            ]
        );
    }

    protected function _initToken($setToken = null) {
        if (null !== $setToken) {
            if (false === $setToken) {
                // security measure for avoid unsetting token twice
                if (!$this->_getSession()->getExpressCheckoutToken()) {
                    throw new \Simi\Simiconnector\Helper\SimiException(__('PayPal Express Checkout Token does not exist.'), 4);
                }
                $this->_getSession()->unsExpressCheckoutToken();
            } else {
                $this->_getSession()->setExpressCheckoutToken($setToken);
            }
            return $this;
        }
        $data = $this->getData();
        $controller = $data['controller'];
        if ($setToken = $controller->getRequest()->getParam('token')) {
            if ($this->_getSession()->getExpressCheckoutToken() && $setToken !== $this->_getSession()->getExpressCheckoutToken()) {
                throw new \Simi\Simiconnector\Helper\SimiException(__('Wrong PayPal Express Checkout Token specified.'), 4);
            }
        } else {
            $setToken = $this->_getSession()->getExpressCheckoutToken();
        }
        return $setToken;
    }

    private function _getSession() {
        return $this->simiObjectManager->get('\Magento\Framework\Session\Generic');
    }
}
