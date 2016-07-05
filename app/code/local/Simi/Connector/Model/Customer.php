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
 * Connector Model Customer
 * 
 * @category    Simi
 * @package     Simi_Connector
 * @author      Simi Developer
 */
class Simi_Connector_Model_Customer extends Simi_Connector_Model_Abstract {

    protected function _helperCustomer() {
        return Mage::helper('connector/customer');
    }

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    protected function _getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
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

    public function checkLogin($data) {
        $session = $this->_getSession();
        try {
            $session->login($data->user_email, $data->user_password);
            return 'success';
        } catch (Exception $e) {
            $this->logout();
            if (is_array($e->getMessage())) {
                return $e->getMessage();
            } else {
                return array($e->getMessage());
            }
        }
    }

    public function checkLoginStatus($data) {
        $user_email = $data->user_email;
        $result = array();
        if (!$user_email) {
            return $this->statusError();
        }
        $customer = $this->getCustomerByEmail($user_email);
        if ($customer && $customer->getId() != null) {
            $customer_session = Mage::getSingleton('customer/session');
            $customer_session->setCustomerAsLoggedIn($customer);
            $information = $this->statusSuccess(array('Login Success'));
            $result['user_id'] = $this->_getSession()->getCustomer()->getId();
            $result['user_email'] = $this->_getSession()->getCustomer()->getEmail();
            $reCustomer = Mage::getSingleton('customer/customer')->load($this->_getSession()->getCustomer()->getId());          
            $result['user_name'] = $reCustomer->getFirstname() . " " . $reCustomer->getLastname();
            $information['data'] = array($result);
            return $information;
        } else {
            return $this->statusError(array($this->_helperCustomer()->__('Customer is not exist')));
        }
    }

    public function _construct() {
        parent::_construct();
    }

    public function getCustomerByEmail($email) {
        return Mage::getModel('customer/customer')->getCollection()
                        ->addFieldToFilter('email', $email)
                        ->getFirstItem();
    }

    public function register($data) {
        $message = array();
        $checkCustomer = $this->getCustomerByEmail($data->user_email);
        if ($checkCustomer->getId()) {
            $message[] = $this->_helperCustomer()->__('Account is already exist');
            $information = $this->statusError($message);
            return $information;
        }

        $name = Mage::helper('connector/checkout')->soptName($data->user_name);
        $customer = Mage::getModel('customer/customer')
                ->setFirstname($name['first_name'])
                ->setLastname($name['last_name'])
                ->setEmail($data->user_email);
        if (isset($data->day) && $data->day != "") {
            $brithday = $data->year . "-" . $data->month . "-" . $data->day;
            $customer->setDob($brithday);
        }

        if (isset($data->taxvat)) {
            $customer->setTaxvat($data->taxvat);
        }

        if (isset($data->gender) && $data->gender) {
            $customer->setGender($data->gender);
        }
        if (isset($data->prefix) && $data->prefix) {
            $customer->setPrefix($data->prefix);
        }

        if (isset($data->suffix) && $data->suffix) {
            $customer->setSuffix($data->suffix);
        }
        //$newPassword = $customer->generatePassword();
        $customer->setPassword($data->user_password);
        try {
            $customer->save();
            $result = array();
            $result['user_id'] = $customer->getId();
            $information = $this->statusSuccess();
            $information['data'] = array($result);
            $session = $this->_getSession();
            if ($customer->isConfirmationRequired()) {
                /** @var $app Mage_Core_Model_App */
                $app = Mage::app();
                /** @var $store  Mage_Core_Model_Store*/
                $store = $app->getStore();
                $customer->sendNewAccountEmail(
                    'confirmation',
                    $session->getBeforeAuthUrl(),
                    $store->getId()
                );
                $information['message'] = array($this->_helperCustomer()->__('Account confirmation is required. Please, check your email.'));
            } else {            
                $information['message'] = array($this->_helperCustomer()->__("Thank you for registering with " . Mage::app()->getStore()->getName() . " store"));
            }            
            return $information;
        } catch (Exception $e) {
            $message = $e->getMessage();
            $information = '';
            if (is_array($message)) {
                $information = $this->statusError($message);
            } else {
                $information = $this->statusError(array($message));
            }
            return $information;
        }
    }

    public function login($data) {
        $result = array();
        try {
            $this->loginCustomer($data->user_email, $data->user_password);
            $this->sycnCometchatAccount($data->user_email, $data->user_password);
            $result['user_id'] = $this->_getSession()->getCustomer()->getId();
            $result['user_email'] = $this->_getSession()->getCustomer()->getEmail();
            $result['user_name'] = $this->_getSession()->getCustomer()->getFirstname() . " " . $this->_getSession()->getCustomer()->getLastname();
            //$result['token'] = $this->_getSession()->getCustomer()->getPasswordHash();
            $result['cart_qty'] = Mage::helper('checkout/cart')->getSummaryCount();
            $information = $this->statusSuccess();
            $information['data'] = array($result);
            return $information;
        } catch (Exception $e) {
            $this->logout();
            $message = $e->getMessage();
            $information = '';
            if (is_array($message)) {
                $information = $this->statusError($message);
            } else {
                $information = $this->statusError(array($message));
            }
            return $information;
        }
    }

    public function loginCustomer($username, $password) {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());

        if ($customer->authenticate($username, $password)) {
            $this->_getSession()->setCustomerAsLoggedIn($customer);
            // $this->renewSession();
            return true;
        }
        return false;
    }

    public function logout() {
        try {
            $this->_getSession()->logout()
                    ->setBeforeAuthUrl(Mage::getUrl());
            $information = $this->statusSuccess();
            return $information;
        } catch (Exception $e) {
            $message = $e->getMessage();
            $information = '';
            if (is_array($message)) {
                $information = $this->statusError($message);
            } else {
                $information = $this->statusError(array($message));
            }
            return $information;
        }
    }

    public function changeUser($data) {
        $result = array();
        $customer = $this->_getSession()->getCustomer();
        // Zend_debug::dump($customer->getData());die();
        $currPass = $data->old_password;
        $newPass = $data->new_password;
        $confPass = $data->com_password;
        
        
        $name = Mage::helper('connector/checkout')->soptName($data->user_name);
        $customerData = array(
            'firstname' => $name['first_name'],
            'lastname' => $name['last_name'],
            'email' => $data->user_email,
        );
        if (isset($data->day) && $data->day != "") {
            $brithday = $data->year . "-" . $data->month . "-" . $data->day;
            $customerData['dob'] = $brithday;
        }
        if (isset($data->taxvat) && $data->taxvat) {
            $customerData['taxvat'] = $data->taxvat;
        }
        if (isset($data->gender) && $data->gender) {
            $customerData['gender'] = $data->gender;
        }

        if (isset($data->prefix) && $data->prefix) {
            $customerData['prefix'] = $data->prefix;
        }

        if (isset($data->suffix) && $data->suffix) {
            $customerData['suffix'] = $data->suffix;
        }

        $errors = NULL;
        if (version_compare(Mage::getVersion(), '1.4.2.0', '<') === true) {
            $customer = Mage::getModel('customer/customer')
                    ->setId($this->_getSession()->getCustomerId())
                    ->setWebsiteId($this->_getSession()->getCustomer()->getWebsiteId());
            $fields = Mage::getConfig()->getFieldset('customer_account');
            foreach ($fields as $code => $node) {
                if ($node->is('update') && isset($customerData[$code])) {
                    $customer->setData($code, $customerData[$code]);
                }
            }
        } else {
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_edit')
                    ->setEntity($customer);
            $customerErrors = $customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
                return $this->statusError($errors);
            } else {
                $customerForm->compactData($customerData);
            }
        }
        if ($data->change_password == 1) {                      
            $customer->setChangePassword(1);
            $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
            if (Mage::helper('core/string')->strpos($oldPass, ':')) {
                list($_salt, $salt) = explode(':', $oldPass);
            } else {
                $salt = false;
            }
            if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                if (strlen($newPass)) {
                    $customer->setPassword($newPass);
                    $customer->setConfirmation($confPass);
                } else {
                    return $this->statusError(array($this->_helperCustomer()->__('New password field cannot be empty')));
                }
            } else {
                return $this->statusError(array($this->_helperCustomer()->__('Invalid current password')));
            }
        }
        $customerErrors = $customer->validate();

        if (is_array($customerErrors)) {
            if (is_array($errors)) {
                $errors = array_merge($errors, $customerErrors);
                return $this->statusError($errors);
            }
            return $this->statusError($customerErrors);
        }
        try {
            $customer->setConfirmation(null);
            $customer->save();
            $this->_getSession()->setCustomer($customer);
            $result['token'] = $customer->getPasswordHash();
            $result['user_name'] = $customer->getFirstname() . " " . $customer->getLastname();
            $result['user_email'] = $customer->getEmail();
            $information = $this->statusSuccess();
            $information['data'] = array($result);
            $information['message'] = array($this->_helperCustomer()->__('The account information has been saved.'));
            return $information;
        } catch (Exception $e) {
            if ($data->change_password == 1) {
                $this->logout();
            }
            $message = $e->getMessage();
            $information = '';
            if (is_array($message)) {
                $information = $this->statusError($message);
            } else {
                $information = $this->statusError(array($message));
            }
            return $information;
        }
    }

    public function getProfile() {
        $result = array();
        $customer = $this->_getSession()->getCustomer();
        $result['user_id'] = $customer->getId();
        $result['user_name'] = $customer->getFirstname() . " " . $customer->getLastname();
        $result['user_email'] = $customer->getEmail();
        $event_name = $this->getControllerName() . '_detail';
        $event_value = array(
            'object' => $this,
        );
        $data_change = $this->changeData($result, $event_name, $event_value);
        $information = $this->statusSuccess();
        $information['data'] = array($data_change);
        return $information;
    }

    public function getAddressUser($data) {
        $result = array();
        $list = array();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $customer = $this->_getSession()->getCustomer();
        // check address billing and shipping
        $billing = $customer->getPrimaryBillingAddress();
        $address_billing_id = $quote->getBillingAddress()->getId();
        $address_shipping_id = $quote->getShippingAddress()->getId();
        if ($billing) {
            $list[] = $this->_helperCustomer()->getAddress($billing, $customer);
            $result[] = $this->_helperCustomer()->getAddressToOrder($billing, $customer, $address_billing_id, $address_shipping_id, $billing->getId());
        }
        $shipping = $customer->getPrimaryShippingAddress();
        if ($shipping) {
            $item = $this->_helperCustomer()->getAddress($shipping, $customer);
            if (!in_array($item, $list)) {
                $result[] = $this->_helperCustomer()->getAddressToOrder($shipping, $customer, $address_billing_id, $address_shipping_id, $shipping->getId());
                $list[] = $item;
            }
        }

        //check adress additional
        $adrress_addition = $customer->getAdditionalAddresses();
        // Zend_debug::dump($adrress_addition->getSize());die();
        foreach ($adrress_addition as $adrr) {
            $item = $this->_helperCustomer()->getAddress($adrr, $customer);
            if (!in_array($item, $list)) {
                $result[] = $this->_helperCustomer()->getAddressToOrder($adrr, $customer, $address_billing_id, $address_shipping_id, $adrr->getId());
                $list[] = $item;
            }
        }
        //check addrress in orders
        // if (isset($data->is_get_order_address) && $data->is_get_order_address == "YES") {
            // $orders = Mage::getModel('sales/order')->getCollection()
                    // ->addFieldToFilter('customer_id', $this->_getSession()->getCustomer()->getId());
            // foreach ($orders as $order) {
                // $shipping = $order->getShippingAddress();
                // $item_shipping = $this->_helperCustomer()->getAddress($shipping, $customer);
                // if (!in_array($item_shipping, $list)) {
                    // $result[] = $this->_helperCustomer()->getAddressToOrder($shipping, $customer, $address_billing_id, $address_shipping_id);
                    // $list[] = $item_shipping;
                // }
                // $billing = $order->getBillingAddress();
                // $item_billing = $this->_helperCustomer()->getAddress($billing, $customer);
                // if (!in_array($item_billing, $list)) {
                    // $result[] = $this->_helperCustomer()->getAddressToOrder($billing, $customer, $address_billing_id, $address_shipping_id);
                    // $list[] = $item_billing;
                // }
            // }
        // }
        $information = $this->statusSuccess();
        $information['data'] = $result;
        return $information;
    }

    public function saveAddress($data) {
        $country = $data->country_code;
        $listState = $this->getStates($country);
        $state_id = null;
        if (count($listState) == 0)
            $check_state = true;
        foreach ($listState as $state) {
            if (in_array($data->state_code, $state)
                    || in_array($data->state_name, $state)) {
                $state_id = $state['state_id'];
                $check_state = true;
                break;
            }
        }
        if (!$check_state) {
            return $this->statusError(array($this->_helperCustomer()->__('State invalid')));
        }
        $address = Mage::helper('connector/customer')->convertDataAddress($data, $state_id);
        $address['id'] = isset($data->address_id) == true ? $data->address_id : null;
        $result = $this->saveAddressCustomer($address);
        if (!$result['result']) {
            return $this->statusError(array($this->_helperCustomer()->__($result['data'])));
        } else {
            $information = $this->statusSuccess();
            $information['data'] = array($result['data']);
            return $information;
        }
    }

    public function saveAddressCustomer($data) {
        $return = array();
        $result = true;
        $errors = false;
        $customer = $this->_getSession()->getCustomer();
        $address = Mage::getModel('customer/address');
        $addressId = $data['id'];
        if (version_compare(Mage::getVersion(), '1.4.2.0', '<') === true) {
            $address->setData($data);
        }
        if ($addressId && $addressId != '') {
            $existsAddress = $customer->getAddressById($addressId);
            if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                $address->setId($existsAddress->getId());
            }
        } else {
            $address->setId(null);
        }

        if (version_compare(Mage::getVersion(), '1.4.2.0', '>=') === true) {
            $addressForm = Mage::getModel('customer/form');
            $addressForm->setFormCode('customer_address_edit')
                    ->setEntity($address);
        }
        try {
            if (version_compare(Mage::getVersion(), '1.4.2.0', '>=') === true) {
                $addressForm->compactData($data);
            }
            $address->setCustomerId($customer->getId());
            $addressErrors = $address->validate();
            if ($addressErrors !== true) {
                $errors = true;
            }

            if (!$errors) {
                $address->save();
                $data['name'] = $address->getName();
                $data['address_id'] = $address->getId();
                $data['street'] = $address->getStreetFull();
                $data['state_name'] = $data['region'];
                $data['state_code'] = $data['region_id'];
                $data['zip'] = $data['postcode'];
                $data['country_code'] = $data['country_id'];
                $data['country_name'] = $data['country_name'];
                $data['phone'] = $data['telephone'];
                $return['result'] = $result;
                $return['data'] = $data;
            } else {
                $result = false;
                $return['result'] = $result;
                $return['data'] = Mage::helper("core")->__('Can not save address customer');
            }
        } catch (Exception $e) {
            $result = false;
            $return['result'] = $result;
            $return['data'] = $e->getMessage();
        }
        return $return;
    }

    public function getStates($code) {
        $list = array();
        if ($code) {
            $states = Mage::getModel('directory/country')->loadByCode($code)->getRegions();
            foreach ($states as $state) {
                $list[] = array(
                    'state_id' => $state->getRegionId(),
                    'state_name' => $state->getName(),
                    'state_code' => $state->getCode(),
                );
            }
        }
        return $list;
    }
	
	//Scott edited to show bookable product option
    public function getCart($data) {
       $width = null;
        $height = null;
        if(isset($data->width)){
            $width = $data->width;
        }
        if(isset($data->height)){
            $height = $data->height;
        }
        $list = array();
        $quote = $this->_getCheckoutSession()->getQuote();
        $allItems = $quote->getAllVisibleItems();
        foreach ($allItems as $item) {
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
                }elseif ($item->getProductType() == "bookable") { //scott add to customize
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
            
            $pro_price = $item->getCalculationPrice();
            if( Mage::helper('tax')->displayCartPriceInclTax() ||  Mage::helper('tax')->displayCartBothPrices()){
                $pro_price = Mage::helper('checkout')->getSubtotalInclTax($item);
            }
				$list[] = array(
					'cart_item_id' => $item->getId(),
					'product_id' => $product->getId(),
					'stock_status' => $product->isSaleable(),
					'product_name' => $product->getName(),
					'product_price' => $pro_price,
					'product_image' => Mage::getSingleton('connector/catalog_product')->getImageProduct($product, null, $width, $height),
					'product_qty' => $item->getQty(),
					'options' => $options,
				);
			
			
			
			
			
			
        }
	
        $this->_getCheckoutSession()->getQuote()->collectTotals();
        $information = $this->statusSuccess();
        $information['data'] = $list;

        $event_name = $this->getControllerName() . '_total';
        $event_value = array(
            'object' => $this,
        );  
        $other_total = array();     
        $total = $this->_getCheckoutSession()->getQuote()->getTotals();
        //hai ta 2082014
        Mage::helper('connector/checkout')->setTotal($total, $other_total);
        //end haita 2082014
        $subTotal = $total['subtotal']->getValue();
        $information['message'] = array($subTotal);
        $data_change = $this->changeData($other_total, $event_name, $event_value);
        $other_total = $data_change;
        $information['other'] = $other_total;
        Mage::getModel('connector/checkout_cart')->checkItemCart($information);
        return $information;
    }

    public function getOrderList($data) {
        $limit = $data->limit;
        $offset = $data->offset;
        $list = array();

        $orders = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('customer_id', $this->_getSession()->getCustomer()->getId())
                ->setOrder('entity_id', 'DESC');

        if ($offset > count($orders))
            return null;
        $check_limit = 0;
        $check_offset = 0;
        foreach ($orders as $order) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;
            $list[] = array(
                'order_id' => $order->getIncrementId(),
                'order_status' => $order->getStatusLabel(),
                'order_date' => $order->getCreatedAt(),
                'recipient' => is_object($order->getShippingAddress()) == true ? $order->getShippingAddress()->getName() : "",
                'order_items' => $this->getProductFromOrderList($order->getAllVisibleItems())
            );
        }
        $information = $this->statusSuccess();
        $information['data'] = $list;
        return $information;
    }

    public function getProductFromOrderList($itemCollection) {
        $productInfo = array();
        foreach ($itemCollection as $item) {
            $productInfo[] = array(
                'product_name' => $item->getName(),
            );
        }
        return $productInfo;
    }

    public function getOrderDetail($data) {
        $id = $data->order_id;
        $width = null;
        if(isset($data->width)){
            $width = $data->width;
        }        
        $height = null;
        if(isset($data->height)){
            $height = $data->height;
        }        
        $detail = array();
        $order = Mage::getModel('sales/order')->loadByIncrementId($id); 
        if (count($order->getData()) == 0) {
            return $this->statusError();
        }
        
        $shipping = $order->getShippingAddress();       
        $billing = $order->getBillingAddress();
        $shippingAddress = array();
        if(is_object($shipping)){
            $shipping_street = $shipping->getStreetFull();      
            $shippingAddress = array(
                'name' => $shipping->getName(),
                'street' => $shipping_street,
                'city' => $shipping->getCity(),
                'state_name' => $shipping->getRegion(),
                'state_code' => $shipping->getRegionCode(),
                'zip' => $shipping->getPostcode(),
                'country_name' => $shipping->getCountryModel()->loadByCode($billing->getCountry())->getName(),
                'country_code' => $shipping->getCountry(),
                'phone' => $shipping->getTelephone(),
                'email' => $order->getCustomerEmail(),
            );
        }           
        
        $billing_street = $billing->getStreetFull();
        $event_name = $this->getControllerName() . '_total';
        $event_value = array(
            'object' => $this,
        );
        $total_v2 = array();
        Mage::helper('connector/customer')->showTotalOrder($order, $total_v2);    
        $detail[] = array(
            'order_id' => $id,
            'order_date' => $order->getCreatedAt(),
            'order_code' => $order->getIncrementId(),
            'order_total' => $order->getGrandTotal(),
            'order_subtotal' => $order->getSubtotal(),
            'tax' => $order->getTaxAmount(),
            's_fee' => $order->getShippingAmount(),
            'order_gift_code' => $order->getCouponCode(),
            'discount' => abs($order->getDiscountAmount()),
            'order_note' => $order->getCustomerNote(),
            'order_items' => $this->getProductFromOrderDetail($order, $width, $height),
            'payment_method' => $order->getPayment()->getMethodInstance()->getTitle(),
            'shipping_method' => $order->getShippingDescription(),
            'card_4digits' => '',
            'shippingAddress' => $shippingAddress,
            'billingAddress' => array(
                'name' => $billing->getName(),
                'street' => $billing_street,
                'city' => $billing->getCity(),
                'state_name' => $billing->getRegion(),
                'state_code' => $billing->getRegionCode(),
                'zip' => $billing->getPostcode(),
                'country_name' => $billing->getCountryModel()->loadByCode($billing->getCountry())->getName(),
                'country_code' => $billing->getCountry(),
                'phone' => $billing->getTelephone(),
                'email' => $order->getCustomerEmail(),
            ),
            'total_v2' => $total_v2,
        );
        if(!is_object($shipping)){
            unset($detail[0]['shippingAddress']);
            unset($detail[0]['shipping_method']);           
        }

        $data_change = $this->changeData($detail[0], $event_name, $event_value);
        $detail[0] = $data_change;
        
        $information = $this->statusSuccess();
        $information['data'] = $detail;
        return $information;
    }

    public function getProductFromOrderDetail($order, $width, $height) {
        $productInfo = array();
        $itemCollection = $order->getAllVisibleItems();
        foreach ($itemCollection as $item) {
            $options = array();
            if ($item->getProductOptions()) {
                $options = $this->getOptions($item->getProductType(), $item->getProductOptions());
				//get options bookable
				if($item->getProductType()==Simi_Simibooking_Helper_Data::BOOKABLE_TYPE_CODE){
					$options=$this->getOptions($item->getProductType(),$item->getProductOptions());
					$options=array_merge($this->getBookingOptions($item),$options);
				}
				//get options Rental
				if($item->getProductType()==Simi_Simibooking_Helper_Data::RESERVATION_TYPE_CODE){
					$options=$this->getOptions($item->getProductType(),$item->getProductOptions());
					$options=array_merge(Mage::helper('simibooking')->renderDates(null, $item, $item->getProduct()),$options);
				}
				
            }
            //Zend_debug::dump($options);die();
            $product_id = $item->getProductId();
            $product = $item->getProduct();
			
            if (version_compare(Mage::getVersion(), '1.7.0.0', '<') === true) {
                $product = Mage::getModel('catalog/product')->load($product_id);
            }
            $image = Mage::getSingleton('connector/catalog_product')->getImageProduct($product, null, $width, $height);
            $productInfo[] = array(
                'product_id' => $product_id,
                'product_name' => $item->getName(),
                'product_price' => $item->getPrice(),
                'product_image' => $image,
                'product_qty' => $item->getQtyOrdered(),
                'options' => $options,
            );
        }

        return $productInfo;
    }

    public function getOptions($type, $options) {
        $list = array();
        if ($type == 'bundle') {
            foreach ($options['bundle_options'] as $option) {
                foreach ($option['value'] as $value) {
                    $list[] = array(
                        'option_title' => $option['label'],
                        'option_value' => $value['title'],
                        'option_price' => $value['price'],
                    );
                }
            }
        }else {
            $optionsList = array();
            if (isset($options['additional_options'])) {
                $optionsList = $options['additional_options'];
            } elseif (isset($options['attributes_info'])) {
                $optionsList = $options['attributes_info'];
            } elseif (isset($options['options'])) {
                $optionsList = $options['options'];
            }
            foreach ($optionsList as $option) {
                $list[] = array(
                    'option_title' => $option['label'],
                    'option_value' => $option['value'],
                    'option_price' => isset($option['price']) == true ? $option['price'] : 0,
                );
            }
        }
        return $list;
    }
	
	
	public function getBookingOptions($item){
		
		$result = array();
        $options=$item->getProductOptions();
		if (isset($options['info_buyRequest'])) {
			$reservationFrom = @$options['info_buyRequest']['aw_booking_from'];
			$reservationTo = @$options['info_buyRequest']['aw_booking_to'];
			$reservationTimeFrom = @$options['info_buyRequest']['aw_booking_time_from'];
			$reservationTimeTo = @$options['info_buyRequest']['aw_booking_time_to'];
			if ($reservationFrom) {
				$from = new Zend_Date(
					$reservationFrom,
					Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
				);
				$to = new Zend_Date(
					$reservationTo,
					Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
				);
				$product = Mage::getModel('catalog/product')->load($item->getProductId());
				$displayTime
					= $product->getAwBookingRangeType() != AW_Booking_Model_Entity_Attribute_Source_Rangetype::DATE;
				if ($displayTime) {
					$timeFrom = AW_Booking_Model_Product_Type_Bookable::convertTime(
						$reservationTimeFrom, AW_Core_Model_Abstract::RETURN_ARRAY
					);
					$timeTo = AW_Booking_Model_Product_Type_Bookable::convertTime(
						$reservationTimeTo, AW_Core_Model_Abstract::RETURN_ARRAY
					);
					$from->setHour(@$timeFrom[0]);
					$from->setMinute(@$timeFrom[1]);
					$to->setHour(@$timeTo[0]);
					$to->setMinute(@$timeTo[1]);
				}
				
				$result[] = array(
					'option_title' => Mage::helper('connector')->__('Reservation from:'),
					'option_value' => Mage::helper('simibooking')->formatDate($from, 'short', displayTime),
				);
				$result[] = array(
					'option_title' => Mage::helper('connector')->__('Reservation to:'),
					'option_value' => Mage::helper('simibooking')->formatDate($to, 'short', $displayTime),
				);
			}
		}
		if (isset($options['options'])) {
			$result = array_merge($result, $options['options']);
		}
		if (isset($options['additional_options'])) {
			$result = array_merge($result, $options['additional_options']);
		}
		if (isset($options['attributes_info'])) {
			$result = array_merge($result, $options['attributes_info']);
		}
		
        return $result;
	}
    public function forgetPassword($data) {
        $email = $data->user_email;
        if (is_null($email)) {
            return $this->statusError();
        } else {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                return $this->statusError(array(Mage::helper('core')->__('Invalid email address.')));
            }
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);
            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    $information = $this->statusError();
                    if (is_array($exception->getMessage())) {
                        $information['message'] = $exception->getMessage();
                    } else {
                        $information['message'] = array($exception->getMessage());
                    }
                    return $information;
                }
                $information = $this->statusSuccess();
                $information['message'] = array(Mage::helper('customer')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->htmlEscape($email)));
                return $information;
            } else {
                $information = $this->statusError(array(Mage::helper('customer')->__('Customer is not exist')));
                return $information;
            }
        }
    }
	
	public function login_frist($data) {
        if(isset($data->user_email) && isset($data->user_password)){
            if($this->_getSession()->isLoggedIn())
                return true;
            try {
                $this->loginByCustomerEmail($data->user_email, $data->user_password);
            } catch (Exception $e) {
               return false;
            }
        }
        return true;
    } 

    public function sycnCometchatAccount($email, $password){        
        $data['email'] = $email; // To get Email of a customer
        $data['password'] = $password; // To get Password of a customer
        $data['firstname'] = $this->_getSession()->getCustomer()->getFirstname(); // To get Firstname of a customer
        $data['lastname'] = $this->_getSession()->getCustomer()->getLastname(); // To get Last name of a customer
        $data['id'] = $this->_getSession()->getCustomer()->getId();

        $dataString = serialize($data);
        $dataString = base64_encode($dataString);
        
        setcookie('SocialEngineLogin', $dataString, 0, "/");

        $requestUrl = Mage::getStoreConfig('socialengineint/sehost') . '/stors/create?info=' . $dataString;
        // $requestUrl = 'http://www.bornbrio.com/brioworld/' . '/stors/create?info=' . $dataString;
        $ch = curl_init();
        $timeout = 0;
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        ob_start();
        curl_exec($ch);
        curl_close($ch);
        $response = ob_get_contents();
        if (empty($key)) {
          $response = @file_get_contents($requestUrl);
        }
        ob_end_clean();
    }
}