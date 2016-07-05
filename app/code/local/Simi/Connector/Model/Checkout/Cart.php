<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Connector
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Connector Model Catalog
 * 
 * @category    Simi
 * @package     Simi_Connector
 * @author      Simi Developer
 */
class Simi_Connector_Model_Checkout_Cart extends Simi_Connector_Model_Abstract {

    protected $_data;

    protected function _initProduct($productId) {
        $storeId = Mage::app()->getStore()->getId();
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId($storeId)
                    ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    public function _getHelperCatalog() {
        return Mage::helper('connector/catalog');
    }

    protected function _getCart() {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    protected function _getQuote() {
        return $this->_getCart()->getQuote();
    }

    protected function _getProduct($productInfo) {
        $product = null;
        if ($productInfo instanceof Mage_Catalog_Model_Product) {
            $product = $productInfo;
        } elseif (is_int($productInfo) || is_string($productInfo)) {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productInfo);
        }
        $currentWebsiteId = Mage::app()->getStore()->getWebsiteId();
        if (!$product
                || !$product->getId()
                || !is_array($product->getWebsiteIds())
                || !in_array($currentWebsiteId, $product->getWebsiteIds())
        ) {
            Mage::throwException(Mage::helper('checkout')->__('The product could not be found.'));
        }
        return $product;
    }

    protected function _getProductRequest($requestInfo) {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object(array('qty' => $requestInfo));
        } else {
            $request = new Varien_Object($requestInfo);
        }

        if (!$request->hasQty()) {
            $request->setQty(1);
        }

        return $request;
    }

    public function changeData($data_change, $event_name, $event_value) {
        $this->_data = $data_change;
        // dispatchEvent to change data
        $this->eventChangeData($event_name, $event_value);
        return $this->getCacheData();
    }

    public function setCacheData($data, $module_name = '') {
        if ($module_name == "simi_connector") {
            $this->_data = $data;
            return;
        }
        if ($module_name == '' || is_null(Mage::getModel('connector/plugin')->checkPlugin($module_name)))
            return;
        $this->_data = $data;
    }

    public function getCacheData() {
        return $this->_data;
    }

    public function addProduct($productInfo, $requestInfo = null) {
        $product = $this->_getProduct($productInfo);
        $request = $this->_getProductRequest($requestInfo);

        $productId = $product->getId();

        if ($product->getStockItem()) {
            $minimumQty = $product->getStockItem()->getMinSaleQty();
            //If product was not found in cart and there is set minimal qty for it
            if ($minimumQty && $minimumQty > 0 && $request->getQty() < $minimumQty
                    && !$this->_getCart()->getQuote()->hasProductId($productId)
            ) {
                $request->setQty($minimumQty);
            }
        }

        if ($productId) {
            try {
                $result = $this->_getCart()->getQuote()->addProduct($product, $request);
            } catch (Mage_Core_Exception $e) {
                $this->_getCheckoutSession()->setUseNotice(false);
                $result = $e->getMessage();
            }
            /**
             * String we can get if prepare process has error
             */
            if (is_string($result)) {
                $redirectUrl = ($product->hasOptionsValidationFail()) ? $product->getUrlModel()->getUrl(
                                $product, array('_query' => array('startcustomization' => 1))
                        ) : $product->getProductUrl();
                $this->_getCheckoutSession()->setRedirectUrl($redirectUrl);
                if ($this->_getCheckoutSession()->getUseNotice() === null) {
                    $this->_getCheckoutSession()->setUseNotice(true);
                }
                Mage::throwException($result);
            }
        } else {
            Mage::throwException(Mage::helper('checkout')->__('The product does not exist.'));
        }

        Mage::dispatchEvent('checkout_cart_product_add_after', array('quote_item' => $result, 'product' => $product));
        $this->_getCart()->getCheckoutSession()->setLastAddedProductId($productId);
        return $result;
    }

    public function addCart($data) {
        $result = $this->addProductToCart($data);
        return $result;
    }

    public function updateCart($data) {
        $items = $data->cart_items;
        $cartData = array();
        foreach ($items as $item) {
            $cartData[$item->cart_item_id] = array('qty' => $item->product_qty);
        }
        $information = $this->statusSuccess();
        try {
            if (count($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = $this->_getCart();
                if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }
                
                if (version_compare(Mage::getVersion(), '1.4.2.0', '>=') === true) {
                    $cartData = $cart->suggestItemsQty($cartData);
                }               
                $cart->updateItems($cartData)
                        ->save();                       
            }           
        } catch (Mage_Core_Exception $e) {
            if (is_array($e->getMessage())) {
                $information = $this->statusError($e->getMessage());
            } else {
                $information = $this->statusError(array($e->getMessage()));
            }
            return $information;
        } catch (Exception $e) {
            if (is_array($e->getMessage())) {
                $information = $this->statusError($e->getMessage());
            } else {
                $information = $this->statusError(array($e->getMessage()));
            }
            return $information;
        }
        $redirect = Mage::getUrl('connector/customer/get_cart/');
        Header('Location: ' . $redirect);
        exit();
        // this->_getCheckoutSession()->setCartWasUpdated(true);
        // $information['data'] = $this->getAllCart();
        // $this->checkItemCart($information);
        
        return $information;
    }

    /*
     * add product to cart on sever.
     * session ItemSimiCart to save id current product be added cart.
     * function getCartParams to change data to standard data Magento
     */

    public function addProductToCart($infoProduct) {
        $cart = $this->_getCart();
        $result = array();
        $product = $this->_initProduct($infoProduct->product_id);
        $information = $this->statusSuccess();
        if (!$product) {
            $information = $this->statusError();
            return $information;
        }

        $params = $this->_getHelperCatalog()->getCartParams($infoProduct, $product->getTypeId());
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
			
		//Scott add to customize add 2 cart bookable product
		$event_name='checkout_cart_add_product_before_save';
		$object= new Varien_Object($params);
		$event_value = array(
            'object' => $object,
            'request_data' => $infoProduct
        );
		
        $data_change = $this->changeData($object, $event_name,$event_value);
		$params=(array)$object->getData();
        //end
        $information = '';
            $this->addProduct($product, $params);
            $cart->save();
            $this->_getCheckoutSession()->setCartWasUpdated(true);

            Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => Mage::app()->getRequest(), 'response' => Mage::app()->getResponse()));

            $inforcart = $this->getAllCart();
			$information=$this->statusSuccess();
            $information['data'] = $inforcart;           
        } catch (Mage_Core_Exception $e) {
            $result = $e->getMessage();
            if (is_array($e->getMessage())) {
                $information = $this->statusError($e->getMessage());
            } else {
                $information = $this->statusError(array($e->getMessage()));
            }
            return $information;
        } catch (Exception $e) {
            if (is_array($e->getMessage())) {
                $information = $this->statusError($e->getMessage());
            } else {
                $information = $this->statusError(array($e->getMessage()));
            }
            return $information;
        }

        $this->checkItemCart($information);
        return $information;
    }

    /*
     * set Coupon code
     */

    public function setCouponCode($data) {
        $couponCode = $data->coupon_code;
        // $oldCouponCode = $this->_getCart()->getQuote()->getCouponCode();
        $return = array();
        
        $information = '';
        // if (!strlen($couponCode) && !strlen($oldCouponCode)) {
        //     return $this->statusError();
        // }
        
        try {
            $this->_getCart()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getCart()->getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                    ->collectTotals()
                    ->save();
            $total = $this->_getCart()->getQuote()->getTotals();
            $return['discount'] = 0;
            if ($total['discount'] && $total['discount']->getValue()) {
                $return['discount'] = abs($total['discount']->getValue());
            }
            $return['grand_total'] = $total['grand_total']->getValue();
            $return['sub_total'] = $total['subtotal']->getValue();
            if (isset($total['tax']) && $total['tax']->getValue()) {
                $tax = $total['tax']->getValue(); //Tax value if present
            } else {
                $tax = 0;
            }
            $return['tax'] = $tax;

            if (strlen($couponCode)) {
                //update 1.10.2014
                $list_shipping = null;
                
                if (!$this->_getCheckoutSession()->getQuote()->isVirtual()) {
                    $list_shipping = Mage::getModel('connector/checkout_shipping')->getMethods();
                }       
                
                $quote = $this->_getCheckoutSession()->getQuote();
                $totalPay = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
                $payment = Mage::getModel('connector/checkout_payment');
                Mage::dispatchEvent('simi_add_payment_method', array('object' => $payment));
                $paymentMethods = $payment->getMethods($quote, $totalPay);
                //list payment methods
                $list_payment = array();
                foreach ($paymentMethods as $method) {
                    $list_payment[] = $payment->getDetailsPayment($method);
                }
                //end update
                if ($couponCode == $this->_getCart()->getQuote()->getCouponCode() && $return['discount'] != 0) {
                    $return['coupon_code'] = (string) $data->coupon_code;
                    $event_name = $this->getControllerName();
                    $event_value = array(
                        'object' => $this,
                    );
                    $data_change = $this->changeData($return, $event_name, $event_value);
                    $information = $this->statusSuccess();
                    //hai ta 2082014
                    $fee_v2 = array();
                    Mage::helper('connector/checkout')->setTotal($total, $fee_v2);
                    $data_change['v2'] = $fee_v2;
                    //end haita 2082014
                    //haita 1.10.2014
                    if ($list_shipping != null){
                        $information['data'] = array(array(
                            'fee' => $data_change,
                            'payment_method_list' => $list_payment,
                            'shipping_method_list' => $list_shipping,
                        ));
                    }else{
                        $information['data'] = array(array(
                            'fee' => $data_change,
                            'payment_method_list' => $list_payment,                     
                        ));
                    }                
                    //end 1.10.2014
                    $information['message'] = array(Mage::helper('connector')->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode)));
                    return $information;
                } else {
                    $return['coupon_code'] = '';
                    $event_name = $this->getControllerName();
                    $event_value = array(
                        'object' => $this,
                    );
                    $data_change = $this->changeData($return, $event_name, $event_value);
                    //hai ta 2082014
                    $fee_v2 = array();
                    Mage::helper('connector/checkout')->setTotal($total, $fee_v2);
                    $data_change['v2'] = $fee_v2;
                    //end haita 2082014
                    $information = $this->statusSuccess();
                    //haita 1.10.2014
                    if ($list_shipping != null){
                        $information['data'] = array(array(
                            'fee' => $data_change,
                            'payment_method_list' => $list_payment,
                            'shipping_method_list' => $list_shipping,
                        ));
                    }else{
                        $information['data'] = array(array(
                            'fee' => $data_change,
                            'payment_method_list' => $list_payment,                     
                        ));
                    }                
                    //end 1.10.2014
                    $information['message'] = array(Mage::helper('connector')->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode)));
                    return $information;
                }
            } else {                
                //update 1.10.2014
                $list_shipping = null;
                
                if (!$this->_getCheckoutSession()->getQuote()->isVirtual()) {
                    $list_shipping = Mage::getModel('connector/checkout_shipping')->getMethods();
                }       
                
                $quote = $this->_getCheckoutSession()->getQuote();
                $totalPay = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
                $payment = Mage::getModel('connector/checkout_payment');
                Mage::dispatchEvent('simi_add_payment_method', array('object' => $payment));
                $paymentMethods = $payment->getMethods($quote, $totalPay);
                //list payment methods
                $list_payment = array();
                foreach ($paymentMethods as $method) {
                    $list_payment[] = $payment->getDetailsPayment($method);
                }
                //end update
                $return['coupon_code'] = '';
                $event_name = $this->getControllerName();
                $event_value = array(
                    'object' => $this,
                );
                $data_change = $this->changeData($return, $event_name, $event_value);
                $information = $this->statusSuccess();
                //hai ta 2082014
                $fee_v2 = array();
                Mage::helper('connector/checkout')->setTotal($total, $fee_v2);
                $data_change['v2'] = $fee_v2;
                //end haita 2082014
                //haita 1.10.2014
                    if ($list_shipping != null){
                        $information['data'] = array(array(
                            'fee' => $data_change,
                            'payment_method_list' => $list_payment,
                            'shipping_method_list' => $list_shipping,
                        ));
                    }else{
                        $information['data'] = array(array(
                            'fee' => $data_change,
                            'payment_method_list' => $list_payment,                     
                        ));
                    }                
                //end 1.10.2014
                $information['message'] = array(Mage::helper('connector')->__('Coupon code was canceled.'));
                return $information;
            }
        } catch (Mage_Core_Exception $e) {
            if (is_array($e->getMessage())) {
                $information = $this->statusError($e->getMessage());
            } else {
                $information = $this->statusError(array($e->getMessage()));
            }
        } catch (Exception $e) {
            if (is_array($e->getMessage())) {
                $information = $this->statusError($e->getMessage());
            } else {
                $information = $this->statusError(array($e->getMessage()));
            }
        }

        return $information;
    }

    public function getAllCart() {
        $list = array();
        $quote = $this->_getCheckoutSession()->getQuote();
        $allItems = $quote->getAllVisibleItems();
        foreach ($allItems as $item) {
            //Zend_debug::dump($item->getData());die();
            $product = $item->getProduct();
            $options = array();
            if (version_compare(Mage::getVersion(), '1.5.0.0', '>=') === true) {
                $helper = Mage::helper('catalog/product_configuration');
                if ($item->getProductType() == "simple") {
                    $options = Mage::helper('connector/checkout')->convertOptionsCart($helper->getCustomOptions($item));
                } elseif ($item->getProductType() == "configurable") {
                    $options = Mage::helper('connector/checkout')->convertOptionsCart($helper->getConfigurableOptions($item));
                } elseif ($item->getProductType() == "bundle") {
                    $options = Mage::helper('connector/checkout')->getOptions($item);
                }elseif ($item->getProductType() == "bookable") {
                    $options = array_merge(Mage::helper('connector/checkout')->getOptions($item),Mage::helper('simibooking')->getBookingAtributes($item));
                }elseif ($item->getProductType() == "reservation") {
                    $options = Mage::helper('connector/checkout')->getOptions($item);
					$options=array_merge(Mage::helper('simibooking')->renderDates(null, $item, $item->getProduct()),$options);
                }
            } else {
                //Zend_debug::dump(get_class($item));die();
                if ($item->getProductType() != "bundle") {
                    $options = Mage::helper('connector/checkout')->getUsedProductOption($item);
                } else {
                    $options = Mage::helper('connector/checkout')->getOptions($item);
                }
            }
				$list[] = array(
					'cart_item_id' => $item->getId(),
					'product_id' => $product->getId(),
					'product_name' => $product->getName(),              
					'product_price' => Mage::app()->getStore()->convertPrice($item->getPrice(), false),                
					'product_image' => Mage::getSingleton('connector/catalog_product')->getImageProduct($product, null, null, null),
					'product_qty' => $item->getQty(),
					'product_max_qty' => Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty(),
					'options' => $options,
				);
			
            
        }
        return $list;
    }

    public function checkItemCart(&$information) {
        $cart = $this->_getCart();
        $message_error = array();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();
			
            if (!$this->_getQuote()->validateMinimumAmount()) {
                $minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                        ->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));

                $warning = Mage::getStoreConfig('sales/minimum_order/description') ? Mage::getStoreConfig('sales/minimum_order/description') : Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);

                $message_error[] = Mage::helper("connector")->__("NOT CHECKOUT"). " " .$warning;
            }
            $messages = array();
            foreach ($cart->getQuote()->getMessages() as $message) {
                if ($message) {                    
                    $messages[] = $message;
                    $message_error[] = Mage::helper("connector")->__("NOT CHECKOUT"). " " . $message->getText();
                }
            }           
        }
        if (count($message_error)) {
            $information['message'] = $message_error;
        }
        $cart->getCheckoutSession()->getMessages(true);
        $this->_getCheckoutSession()->setCartWasUpdated(true);
    }

}