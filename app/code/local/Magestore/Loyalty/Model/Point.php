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
 * @package     Magestore_Loyalty
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Loyalty Point Model
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @author      Magestore Developer
 */
class Magestore_Loyalty_Model_Point extends Simi_Connector_Model_Checkout
{
	/**
	 * Current point
	 * 
	 */
	// public function getPointCurrent(){
	// 	$store_id = Mage::app()->getStore()->getId();
 //        $customerId = Mage::getModel('customer/session')->getCustomerId();
 //        if (Mage::getStoreConfig('rewardpoints/default/flatstats', $store_id)){
 //            $reward_flat_model = Mage::getModel('rewardpoints/flatstats');
 //            return $reward_flat_model->collectPointsCurrent($customerId, $store_id)+0;
 //        }        
 //        $reward_model = Mage::getModel('rewardpoints/stats');
 //        return $reward_model->getPointsCurrent($customerId, $store_id)+0;
	// }

	/**
	 * get rewardpoint info
	 * 
	 */
	public function getRewardInfo($data)
	{
		$list = array();
		// Collect Info - Customer Points (if logged in)
		$session  = Mage::getSingleton('customer/session');
		// $customer = $session->getCustomer();
		$groupId  = $session->isLoggedIn() ? $session->getCustomerGroupId() : Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID);
		$helper   = Mage::helper('rewardpoints');		
		$point = Mage::getBlockSingleton('rewardpoints/dashboard');
		if ($session->isLoggedIn()) {
			$list['loyalty_point'] = ($point->getPointsCurrent()) ? $point->getPointsCurrent() : 0;
			$list['loyalty_balance'] = '1';
			$list['loyalty_redeem'] = '2';
            $group = Mage::getModel('customer/group')->load($groupId);
            $list['loyalty_level'] = $helper->__("Your Current Membership level is %s", $group->getCode());
			// $list['loyalty_image'] = $helper->getImage();	
			// $list['loyalty_image'] = "http://demo.magestore.com/simicart/simipos3/skin/frontend/base/default/images/rewardpoints/point.png";	
		}
       
		// $list['is_notification'] = 1;
		// $list['expire_notification'] = 0;
		$discount = 1;
		$discount = Mage::helper('core')->formatPrice($discount,false);
		$spendingPoint = Mage::getStoreConfig('rewardpoints/default/points_money', Mage::app()->getStore()->getId());
		$list['earning_label']  = $helper->__('Total Brio coins Spent');
		$list['earning_policy'] = ($point->getPointsLost()) ? $point->getPointsLost() : '0';
		$list['spending_label']  = $helper->__('Brio coins waiting for validation');		
		$list['spending_policy'] = ($point->getPointsWaitingValidation()) ? $point->getPointsWaitingValidation() : '0';
		$list['spending_point'] = (float)$spendingPoint;
		$list['spending_discount'] = $discount;


		// $list['start_discount'] = "$10";
		// $list['spending_min'] = 1;
			
		// $list['policies'] = array('1','2','3');
		// $list['loyalty_point'] = 500;
		// $list['loyalty_balance'] = "500 Points";
		// $list['loyalty_redeem'] = "$5,000.00";
		// $list['loyalty_image'] = "http://demo.magestore.com/simicart/simipos3/skin/frontend/base/default/images/rewardpoints/point.png";
		// $list['is_notification'] = 1;
		// $list['expire_notification'] = 0;
		// $list['earning_label'] = "How you can earn points";
		// $list['earning_policy'] ="Each $500.00 spent for your order will earn 2 Points.";
		// $list['spending_label'] = "How you can spend points";
		// $list['spending_policy'] = "Each 1 Point can be redeemed for $10.00.";
		// $list['spending_point'] = 1;
		// $list['spending_discount'] =  "$10.00";
		// $list['start_discount'] = "$10.00";
		// $list['spending_min'] = 1;
		// $list['policies'] = array(
		// "A transaction will expire after 2 days since its creating date.",
		// "Reach 1 Point to start using your balance for your purchase.",
		// "Maximum 2 Points are allowed to spend for an order."
		// );
		
		// Return Data formatted
		$information = $this->statusSuccess();
        $information['data'] = $list;
        return $information;
	}
	
	/*
     *  history point
     */
	public function getHistory($data)
	{		
		$list = array();
        // Collect Info - Customer Points (if logged in)
        $session  = Mage::getSingleton('customer/session');
        
		$collection = Mage::getModel('rewardpoints/stats')->getCollection()
            ->addClientFilter(Mage::getSingleton('customer/session')->getCustomer()->getId());
        $collection->getSelect()->order('rewardpoints_account_id DESC');
		
		$limit = $data->limit ? $data->limit : null;
        $offset = $data->offset ? $data->offset : null;
        $collection->getSelect()->limit($limit, $offset);
               
        foreach ($collection as $point) { 
        	$list[] = array(
        	    'title'        => Mage::helper('loyalty')->getTypeOfPoint($point),
        	    'point_amount' => $point->getData('points_current'),
        	    'point_label' => $point->getData('points_current'),
        	    'point_used'  => $point->getData('points_spent'),
        	    'created_time' => $point->getData('date_start').' 00:00:00',
        	    // 'expiration_date' => $point->getData('date_start').' 00:00:00',
        	    'expiration_date'  => $point->getData('date_end') ? $point->getData('date_end').' 00:00:00' : '',   
        	    'status' => '1',     	    
        	);        	
        }        
        // Return Data formatted
        $information = $this->statusSuccess();
        $information['message'] = array($collection->getSize());
        $information['data'] = $list;
        return $information;
	}

	/*
     *  remove point
     */
    public function removePoint($data){
    	Mage::getSingleton('rewardpoints/session')->setProductChecked(0);
        Mage::helper('rewardpoints/event')->setCreditPoints(0);
        Mage::helper('checkout/cart')->getCart()->getQuote()
                ->setRewardpointsQuantity(NULL)
                ->setRewardpointsDescription(NULL)
                ->setBaseRewardpoints(NULL)
                ->setRewardpoints(NULL)
                ->save(); 

        $customer_cart = Mage::getModel('loyalty/customer')->getCart($data);    
        return $customer_cart;        
    }

    /*
     *  apply point
     */
    public function applyPoint($data){
        $points_value = $data->point;
    	$session = Mage::getSingleton('core/session');        
        if (Mage::getStoreConfig('rewardpoints/default/max_point_used_order', Mage::app()->getStore()->getId())){
            if ((int)Mage::getStoreConfig('rewardpoints/default/max_point_used_order', Mage::app()->getStore()->getId()) < $points_value){
                $points_max = (int)Mage::getStoreConfig('rewardpoints/default/max_point_used_order', Mage::app()->getStore()->getId());
                return $this->statusError(array(Mage::helper('loyalty')->__('You tried to use %s brio coins, but you can use a maximum of %s brio coins per shopping cart.', ceil($points_value), $points_max)));                
                $points_value = $points_max;
            }
        }
        $quote_id = Mage::helper('checkout/cart')->getCart()->getQuote()->getId();
        Mage::getSingleton('rewardpoints/session')->setProductChecked(0);
        Mage::getSingleton('rewardpoints/session')->setShippingChecked(0);
        Mage::helper('rewardpoints/event')->setCreditPoints($points_value);        
        Mage::helper('checkout/cart')->getCart()->getQuote()
                ->setRewardpointsQuantity($points_value)
                ->save();    

        $customer_cart = Mage::getModel('loyalty/customer')->getCart($data);    
        return $customer_cart;
    }
	
	/*
     *  spend point
     */
	public function spendPoints($data)
	{
		$list = array();
		Mage::app()->getRequest()->setControllerModule('Simi_Connector');
		// Checkout session: spend points
		$session = Mage::getSingleton('checkout/session');
		if($data->usepoint>0)
			$this->applyPoint($data->usepoint);
		else
			$this->removePoint();
        // Return Total Information
        $quote = $session->getQuote();
        $quote->collectTotals()->save();
        
        // Total checkout
        $total = $quote->getTotals();
        $grandTotal = $total['grand_total']->getValue();
        $subTotal = $total['subtotal']->getValue();
        $discount = 0;
        if (isset($total['discount']) && $total['discount']) {
            $discount = abs($total['discount']->getValue());
        }
        if (isset($total['tax']) && $total['tax']->getValue()) {
            $tax = $total['tax']->getValue();
        } else {
            $tax = 0;
        }
        if ($quote->getCouponCode()) {
        	$coupon = $quote->getCouponCode();
        } else {
        	$coupon = '';
        }
        $total_data = array(
            'sub_total' => $subTotal,
            'grand_total' => $grandTotal,
            'discount' => $discount,
            'tax' => $tax,
            'coupon_code' => $coupon,
        );
        $fee_v2 = array();
        Mage::helper('connector/checkout')->setTotal($total, $fee_v2);
        $total_data['v2'] = $fee_v2;
        $list['fee'] = $this->changeData($total_data, 'connector_checkout_get_order_config_total', array('object' => $this));
        
        // Payment
        $totalPay = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
        $payment = Mage::getModel('connector/checkout_payment');
        Mage::dispatchEvent('simi_add_payment_method', array('object' => $payment));
        $paymentMethods = $payment->getMethods($quote, $totalPay);
        $list_payment = array();
        foreach ($paymentMethods as $method) {
            $list_payment[] = $payment->getDetailsPayment($method);
        }
		$list['payment_method_list'] = $this->changeData($list_payment, 'simicart_change_payment_detail', array('object' => $this));
        
		Mage::app()->getRequest()->setControllerModule('Magestore_Loyalty');
		// Return Data formatted
        $information = $this->statusSuccess();
        $information['data'] = array($list);
        return $information;
	}
	
	public function saveSettings($data)
	{
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customerId     = Mage::getSingleton('customer/session')->getCustomerId();
		    $rewardAccount  = Mage::getModel('rewardpoints/customer')->load($customerId, 'customer_id');
            if (!$rewardAccount->getId()) {
                $rewardAccount->setCustomerId($customerId)
                    ->setData('point_balance', 0)
                    ->setData('holding_balance', 0)
                    ->setData('spent_balance', 0);
            }
            $rewardAccount->setIsNotification((boolean)$data->is_notification)
                ->setExpireNotification((boolean)$data->expire_notification);
            try {
            	$rewardAccount->save();
            } catch (Exception $e) {
            	return $this->statusError(array($e->getMessage()));
            }
		} else {
			return $this->statusError(array(Mage::helper('loyalty')->__('Your session has been expired. Please relogin and try again.')));
		}
		// Return Data formatted
		$information = $this->statusSuccess();
        $information['data'] = array('success' => 1);
        return $information;
	}

	/*
     * get referred collection
     */
	public function getReferredCollection(){
		$referred = Mage::getResourceModel('rewardpoints/referral_collection')
            ->addClientFilter(Mage::getSingleton('customer/session')->getCustomer()->getId());
        $referred->getSelect()->order('rewardpoints_referral_id DESC');
        return $referred;
	}

	/*
     * get point referred list
     */
    public function referList($data) {
        $limit = $data->limit;
        $offset = $data->offset;
        $collection = $this->getReferredCollection();       
        $information = $this->getReferedArray($collection, $offset, $limit);
        return $information;
    }

    /*
     *  change referred list to array
     */
    public function getReferedArray($collection, $offset, $limit, $message = null) {
        $list = array();
        $collection->setPageSize($offset + $limit);
        $total = $collection->getSize();
        if ($offset > $total)
            return $this->statusError(array('No information'));
        $check_limit = 0;
        $check_offset = 0;
        foreach ($collection as $refer) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;            
            $info = array(
                'id' => $refer->getId(),
                'full_name' => $refer->getData('rewardpoints_referral_name'),
                'email' => $refer->getData('rewardpoints_referral_email'),
                'first_order' => $refer->getData('rewardpoints_referral_status') == 1 ?
                				 Mage::helper('loyalty')->__('Yes') : 
                				 Mage::helper('loyalty')->__('No'),
   		  );
            $list[] = $info;
        }
        if(!$message)
        	$message = array($total);
        $information = $this->getSuccessResponse($message, $list);
        // $observerObject->information = $information;
        // $observerObject->collection = $collection;
        // Mage::dispatchEvent('loyalty_get_referred_list_after', array('object' => $observerObject));
        // $information = $observerObject->information;
        return $information;
    }

    public function refer($data){
	    if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
		    return $this->statusError(array(Mage::helper('loyalty')->__('Customer has not logged in.')));
		}
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $session = Mage::getSingleton('core/session');
            $referInfo = $data->referinfo;           
            $customerSession = Mage::getSingleton('customer/session');
            //$errors = array();            
            $information = $this->statusSuccess();
            try {
                foreach ($referInfo as $refer){                	
                    $name = trim((string) $refer->name);
                    $email = trim((string) $refer->email);
                    $no_errors = true;
                    if (!Zend_Validate::is($email, 'EmailAddress')) {
                        //Mage::throwException($this->__('Please enter a valid email address.'));
                        //$errors[] = $this->__('Wrong email address (%s).', $email);                        
                        return $this->statusError(array(Mage::helper('loyalty')->__('Wrong email address (%s).', $email)));                
                        $no_errors = false;
                    }
                    if ($name == ''){
                        //Mage::throwException($this->__('Please enter your friend name.'));
                        //$errors[] = $this->__('Friend name is required for (%s) on line %s.', $email, ($key_email+1));                        
                        return $this->statusError(array(Mage::helper('loyalty')->__('Friend name is required for email: %s on line %s.', $email, ($key_email+1))));
                        $no_errors = false;
                    }
                    
                    if ($no_errors){
                        $referralModel = Mage::getModel('rewardpoints/referral');

                        $customer = Mage::getModel('customer/customer')
                                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                                        ->loadByEmail($email);

                        if ($referralModel->isSubscribed($email) || $customer->getEmail() == $email) {                            
                            return $this->statusError(array(Mage::helper('loyalty')->__('Email %s has been already submitted.', $email)));
                        } else {
                            if ($referralModel->subscribe($customerSession->getCustomer(), $email, $name)) {                                
                                $collection = $this->getReferredCollection();
			                    $offset = 0;
			                    $limit = 10;
						        $message = array(Mage::helper('loyalty')->__('Email(s) was successfully invited.'));
			                    $information = $this->getReferedArray($collection, $offset, $limit, $message);
			                    // return $information;
                            } else {                                
                                return $this->statusError(array(Mage::helper('loyalty')->__('There was a problem with the invitation email %s.', $email)));
                            }
                        }
                    }                 
                }
                return $information;
                
            }
            catch (Mage_Core_Exception $e) {
            	return $this->statusError(array(Mage::helper('loyalty')->__('%s', $e->getMessage())));                
            }
            catch (Exception $e) {
            	return $this->statusError(array(Mage::helper('loyalty')->__('There was a problem with the invitation.')));
            }
        }
    }

    /*
     * get sharing collection
     */
	public function getSharingCollection(){
		$shared = Mage::getResourceModel('j2trewardshare/share_collection')
            ->addClientFilter(Mage::getSingleton('customer/session')->getCustomer()->getId());
        return $shared;
	}
    
    /*
     * get point referred list
     */

    public function sharingList($data) {
        $limit = $data->limit;
        $offset = $data->offset;               
        $collection = $this->getSharingCollection();       
        $information = $this->getSharingArray($collection, $offset, $limit);
        return $information;
    }

    /*
     *  change referred list to array
     */
    public function getSharingArray($collection, $offset, $limit, $message = null) {
        $list = array();
        $collection->setPageSize($offset + $limit);
        $total = $collection->getSize();
        if ($offset > $total)
            return $this->statusError(array('No information'));
        $check_limit = 0;
        $check_offset = 0;
        foreach ($collection as $share) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;            
            $info = array(
                'id' => $share->getId(),
                'full_name' => $share->getReceiverName(),
                'email' => $share->getReceiverEmail(),
                'point_share' => $share->getPointsShare(),
                'status' => $share->getStatusName(),
                'is_cancel' => $share->getStatusName() == J2t_Rewardshare_Model_Share::STATUS_NEW ? '1' : '0',
   		  );
            $list[] = $info;
        }
        if(!$message)
        	$message = array($total);
        $information = $this->getSuccessResponse($message, $list);        
        // $observerObject->information = $information;
        // $observerObject->collection = $collection;
        // Mage::dispatchEvent('loyalty_get_referred_list_after', array('object' => $observerObject));
        // $information = $observerObject->information;
        return $information;
    }

    public function getSuccessResponse($message, $data){
    	$information = $this->statusSuccess();
        $information['message'] = $message;
        $information['data'] = $data;
        return $information;
    }

    /*
     *  share point
     */

    public function share($data) {
        $session = Mage::getSingleton('core/session');
        $email = trim((string) $data->email);
        $name = trim((string) $data->name);
        $points = (int) trim($data->points);
        $customerSession = Mage::getSingleton('customer/session');
        try {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                return $this->statusError(array(Mage::helper('loyalty')->__('Please enter a valid email address.')));
            }
            if ($name == ''){
                return $this->statusError(array(Mage::helper('loyalty')->__('Please enter your friend name.')));
            }
            $shareModel = Mage::getModel('j2trewardshare/share');
            $customer = Mage::getModel('customer/customer')
                            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                            ->loadByEmail($email);
            if (!$customer->getId()){                
                return $this->statusError(array(Mage::helper('loyalty')->__('This user does not exist.')));
            } else {
                $points_available = Mage::getModel('rewardpoints/stats')->getPointsCurrent($customerSession->getCustomer()->getId(), Mage::app()->getStore()->getId());
                if ($points_available < $points){                    
                    return $this->statusError(array(Mage::helper('loyalty')->__('You want to send %s points but you have only %s points.', $points, $points_available)));
                } else if ($customer->getId() == $customerSession->getCustomer()->getId()) {                   
                    return $this->statusError(array(Mage::helper('loyalty')->__('You cannot send points to yourself.')));                    
                } else if ($shareModel->share($customerSession->getCustomer(), $email, $name, $customer, $points)) {
                    //refresh flat table                    
                    $customer = $customerSession->getCustomer();
                    /*if ($store_id = $customer->getStore()->getId()){
                        Mage::getModel('rewardpoints/flatstats')->processRecordFlat($customer->getId(), $store_id);
                    } else {*/
                        $allStores = Mage::app()->getStores();
                        foreach ($allStores as $_eachStoreId => $val) {
                            Mage::getModel('rewardpoints/flatstats')->processRecordFlat($customer->getId(), Mage::app()->getStore($_eachStoreId)->getId());
                        }
                    //}  
                    $collection = $this->getSharingCollection();
                    $offset = 0;
                    $limit = 10;
			        $message = array(Mage::helper('loyalty')->__('This email was successfully sent.'));
                    $information = $this->getSharingArray($collection, $offset, $limit, $message);                    
			        return $information;
                } else {                    
                    return $this->statusError(array(Mage::helper('loyalty')->__('There was a problem with the point sharing.')));                    
                }
            }
        }
        catch (Mage_Core_Exception $e) {
        	return $this->statusError(array(Mage::helper('loyalty')->__('%s', $e->getMessage())));            
        }
        catch (Exception $e) {            
            return $this->statusError(array(Mage::helper('loyalty')->__('There was a problem with the point sharing.')));
        }       
    }

    /*
     *  cancel share point
     */
    public function cancelShare($data)
    {
        $session = Mage::getSingleton('core/session');
        $sharePointsId = $data->share_id;
        $model = Mage::getModel('j2trewardshare/share');
        $model->load($sharePointsId);
        $customerSession = Mage::getSingleton('customer/session');        
        try {
            if (!$model->getId()) {
            	return $this->statusError(array(Mage::helper('loyalty')->__('Unable to find the operation.')));                
            } else if ($customerSession->getCustomer()->getId() != $model->getCustomerId()){
            	return $this->statusError(array(Mage::helper('loyalty')->__('This user cannot cancel the operation.')));                
            } else if ($model->getStatus() == J2t_Rewardshare_Model_Share::STATUS_NEW){                
                $model->delete();
                // refresh flat table
                $customer = $customerSession->getCustomer();
                /*if ($store_id = $customer->getStore()->getId()){
                    Mage::getModel('rewardpoints/flatstats')->processRecordFlat($customer->getId(), $store_id);
                } else {*/
                    $allStores = Mage::app()->getStores();
                    foreach ($allStores as $_eachStoreId => $val) {
                        Mage::getModel('rewardpoints/flatstats')->processRecordFlat($customer->getId(), Mage::app()->getStore($_eachStoreId)->getId());
                    }
                //}                
		        $collection = $this->getSharingCollection();
                $offset = 0;
                $limit = 10;
		        $message = array(Mage::helper('loyalty')->__('Operation has successfully been cancelled.'));
                $information = $this->getSharingArray($collection, $offset, $limit, $message);  
                return $information;     
            } else {
            	return $this->statusError(array(Mage::helper('loyalty')->__('Unable to cancel the operation.')));                
            }
        }
        catch (Mage_Core_Exception $e) {
        	return $this->statusError(array(Mage::helper('loyalty')->__('%s', $e->getMessage())));            
        }
        catch (Exception $e) {
        	return $this->statusError(array(Mage::helper('loyalty')->__('There was a problem with this cancellation.')));            
        }
        
        $this->_redirectReferer();
    }

}
