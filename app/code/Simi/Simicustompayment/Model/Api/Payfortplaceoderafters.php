<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Simicustompayment\Model\Api;

use Magento\Sales\Model\Order;

class Payfortplaceoderafters extends \Simi\Simiconnector\Model\Api\Apiabstract {

	public function setBuilderQuery() {

	}

	public function store() {
		$data           = $this->getData();
		$contents_array = $data['contents_array'];
		$result         = array();
		if ( $contents_array && isset( $contents_array['order_information'] ) ) {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$storeManager  = $objectManager->get( '\Magento\Store\Model\StoreManagerInterface' );
			$baseUrl       = $storeManager->getStore()->getBaseUrl( \Magento\Framework\UrlInterface::URL_TYPE_WEB );

			$order_information = $contents_array['order_information'];
			$order_id          = $order_information['id'];
			$checkoutSession   = $this->simiObjectManager->create( 'Magento\Checkout\Model\Session' );

			$order   = $this->simiObjectManager->create( 'Magento\Sales\Model\Order' )->load( $order_id );
			$payment = $order->getPayment();
			$method  = $payment->getMethodInstance();
			//echo $method->getTitle(); // Cash On Delivery
			//echo $method->getCode(); // cashondelivery

			if ( $method->getCode() == 'payfort_fort_sadad' ) {
				$result['url_action'] = $this->simiObjectManager->get( 'Magento\Framework\UrlInterface' )
				                                         ->getUrl( 'simicustompayment/api/placement', array(
						                                         '_secure'         => true,
						                                         'OrderID'         => base64_encode( $order_id ),
						                                         'LastRealOrderId' => base64_encode( $order->getIncrementId() )
					                                         )
				                                         );
			}

			if ( $method->getCode() == 'payfort_fort_cc'){
				$result = $this->getPaymentData($order);
			}

		}

		return $result;
	}

	public function getPaymentData($order){
		$simiObjectManager = $this->simiObjectManager;
		$orderConfig = $simiObjectManager->get('\Magento\Sales\Model\Order\Config') ;
		$checkoutSession = $simiObjectManager->get('Magento\Checkout\Model\Session');
		$payfortModel = $simiObjectManager->get('\Payfort\Fort\Model\Payment');
		$helperFort = $simiObjectManager->get('\Payfort\Fort\Helper\Data');

		$order_is_ok = true;
		$order_error_message = '';

		//$order = $simiObjectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($data['invoice_number']);
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
			$form_url = $arrPaymentPageData['url'];

			$paymentMethod= $order->getPayment()->getMethod();

			$order->addStatusHistoryComment( 'PayfortFort :: redirecting to payment page with Method: '.$paymentMethod );

			$order->save();
//			$checkoutSession->setLastSuccessQuoteId($data['quote_id']);
			$checkoutSession->setLastOrderId($order->getIncrementId());
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