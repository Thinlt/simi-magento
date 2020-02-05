<?php

/**
 *
 * Copyright © 2016 Simicommerce. All rights reserved.
 */

namespace Simi\Simicustompayment\Controller\Api;
use Magento\Sales\Model\Order;
class Placement extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $simiObjectManager = $this->_objectManager;
        $session = $simiObjectManager->get('Magento\Customer\Model\Session');
        $checkoutSession = $simiObjectManager->create('Magento\Checkout\Model\Session');
        $checkoutSession->setOrderid(base64_decode($this->getRequest()->getParam('OrderID')));
        $checkoutSession->setMerchantid(base64_decode(($this->getRequest()->getParam('MerchantID'))));
        $checkoutSession->setAmount(base64_decode($this->getRequest()->getParam('Amount')));
        $checkoutSession->setCurrencycode(base64_decode($this->getRequest()->getParam('CurrencyCode')));
        $checkoutSession->setTransactiontype(base64_decode($this->getRequest()->getParam('TransactionType')));
        $checkoutSession->setTransactiondatetime(base64_decode($this->getRequest()->getParam('TransactionDateTime')));
        $checkoutSession->setOrderdescription(base64_decode($this->getRequest()->getParam('OrderDescription')));
        $checkoutSession->setCity(base64_decode($this->getRequest()->getParam('City')));
        $checkoutSession->setState(base64_decode($this->getRequest()->getParam('State')));
        $checkoutSession->setPostcode(base64_decode($this->getRequest()->getParam('PostCode')));
        $checkoutSession->setLastRealOrderId(base64_decode($this->getRequest()->getParam('LastRealOrderId')));
        // $order = $checkoutSession->getLastRealOrder();
        // // var_dump($order->getData());die;
        // $store = $simiObjectManager->get('\Magento\Store\Model\StoreManagerInterface');
        // $base_url = $store->getStore()->getBaseUrl();
        // $url = $base_url.'payfortfort/payment/getPaymentData';
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $data = curl_exec ($ch);
        // curl_close ($ch);
        // echo $data;die('xx');
        $data = $this->getPaymentData();
        // var_dump($data);die;
        $this->_redirect('payfortfort/payment/redirect');
    }

    public function getPaymentData(){
        $simiObjectManager = $this->_objectManager;
        $orderConfig = $simiObjectManager->get('\Magento\Sales\Model\Order\Config') ;
        $checkoutSession = $simiObjectManager->get('Magento\Checkout\Model\Session');
        $payfortModel = $simiObjectManager->get('\Payfort\Fort\Model\Payment');
        $helperFort = $simiObjectManager->get('\Payfort\Fort\Helper\Data');

        $order_is_ok = true;
        $order_error_message = '';
        if( !($order = $checkoutSession->getLastRealOrder()) )
            $order_error_message = __( 'Couldn\'t extract order information.' );

        elseif( $order->getState() != Order::STATE_NEW )
            $order_error_message = __( 'Order was already processed or session information expired.' );

        elseif( !($additional_info = $order->getPayment()->getAdditionalInformation())
             or !is_array( $additional_info ) )
            $order_error_message = __( 'Couldn\'t extract payment information from order.' );

        if( !empty( $order_error_message ) )
            $order_is_ok = false;
        $form_data  = '';
        $form_url   = '';
        if( $order_is_ok )
        {
            $helper = $helperFort;
            if($helperFort->isMerchantPageMethod($order)) {
                $arrPaymentPageData = $helperFort->getPaymentRequestParams($order, $helper::PAYFORT_FORT_INTEGRATION_TYPE_MERCAHNT_PAGE);
            }
            elseif($helperFort->isMerchantPageMethod2($order)) {
                $arrPaymentPageData = $helperFort->getPaymentRequestParams($order, $helper::PAYFORT_FORT_INTEGRATION_TYPE_MERCAHNT_PAGE2);
            }
            else {
                $arrPaymentPageData = $helperFort->getPaymentRequestParams($order, $helper::PAYFORT_FORT_INTEGRATION_TYPE_REDIRECTION);
            }
            
            $form_data = $arrPaymentPageData['params'];
            $form_url = $arrPaymentPageData['url'];
        
            $paymentMethod= $order->getPayment()->getMethod();
            
            $order->addStatusHistoryComment( 'PayfortFort :: redirecting to payment page with Method: '.$paymentMethod );

            $order->save();
        }
        else{
            $result = array(
                    'success' => false, 
                    'error_message' => $order_error_message,
            );
            return $result;
        }
        $result = array(
            'success' => true,
            'error_message' => $order_error_message,
            'order_id'  => $order->getIncrementId(),
            'params'  => $form_data,
            'url'  => $form_url,
        );
        return $result;
    }
}
