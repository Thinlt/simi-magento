<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Simicustompayment\Observer;

use Magento\Sales\Model\Order;
use Magento\Framework\Event\ObserverInterface;

class SimiconnectorAfterPlaceOrder implements ObserverInterface {

    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        $this->simiObjectManager = $simiObjectManager;
    }
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $orderObject = $observer->getObject();
        $data = $orderObject->order_placed_info;
        if (isset($data['payment_method'])) {
            $orderObject = $observer->getObject();
            if($data['payment_method'] == "payfort_fort_sadad"){
                $data = $orderObject->order_placed_info;
                $data['url_action'] = $this->getOrderPlaceRedirectUrl($data['invoice_number']);
                $orderObject->order_placed_info = $data;
            }
            if($data['payment_method'] == "payfort_fort_cc"){
                $data =  $this->getPaymentData($data);
                $data['payment_method'] = 'payfort_fort_cc';
                $orderObject->order_placed_info = $data;
            }
        }
    }

    public function getOrderPlaceRedirectUrl($order_id) {

        $checkoutSession = $this->simiObjectManager->create('Magento\Checkout\Model\Session');
        return $this->simiObjectManager->get('Magento\Framework\UrlInterface')
            ->getUrl('simicustompayment/api/placement', array('_secure' => true,
            'OrderID' => base64_encode($order_id),
            'Amount' => base64_encode($checkoutSession->getAmount()),
            'CurrencyCode' => base64_encode($checkoutSession->getCurrencycode()),
            'TransactionType' => base64_encode($checkoutSession->getTransactiontype()),
            'TransactionDateTime' => base64_encode($checkoutSession->getTransactiondatetime()),
            'CallbackURL' => base64_encode($checkoutSession->getCallbackurl()),
            'OrderDescription' => base64_encode($checkoutSession->getOrderdescription()),
            'CustomerName' => base64_encode($checkoutSession->getCustomername()),
            'Address1' => base64_encode($checkoutSession->getAddress1()),
            'Address2' => base64_encode($checkoutSession->getAddress2()),
            'Address3' => base64_encode($checkoutSession->getAddress3()),
            'Address4' => base64_encode($checkoutSession->getAddress4()),
            'City' => base64_encode($checkoutSession->getCity()),
            'State' => base64_encode($checkoutSession->getState()),
            'PostCode' => base64_encode($checkoutSession->getPostcode()),
            'LastRealOrderId' => base64_encode($checkoutSession->getLastRealOrderId())
        ));
    }

    public function getPaymentData($data){
        $simiObjectManager = $this->simiObjectManager;
        $orderConfig = $simiObjectManager->get('\Magento\Sales\Model\Order\Config') ;
        $checkoutSession = $simiObjectManager->get('Magento\Checkout\Model\Session');
        $payfortModel = $simiObjectManager->get('\Payfort\Fort\Model\Payment');
        $helperFort = $simiObjectManager->get('\Payfort\Fort\Helper\Data');

        $order_is_ok = true;
        $order_error_message = '';

        $order = $simiObjectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($data['invoice_number']);
        if( !$order->getId() )
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
            // $form_data['return_url'] = $this->simiObjectManager->get('Magento\Framework\UrlInterface')
            // ->getUrl('simicustompayment/api/placementResponse', array('_secure' => true));
            $form_url = $arrPaymentPageData['url'];
        
            $paymentMethod= $order->getPayment()->getMethod();
            
            $order->addStatusHistoryComment( 'PayfortFort :: redirecting to payment page with Method: '.$paymentMethod );

            $order->save();
            $checkoutSession->setLastSuccessQuoteId($data['quote_id']);
            $checkoutSession->setLastOrderId($data['invoice_number']);
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
