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
 * Loyalty Status Model
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @author      Magestore Developer
 */
class Magestore_Loyalty_Model_Customer extends Simi_Connector_Model_Customer
{
	const XML_PATH_DESIGN_BIG_INLINE_IMAGE_SHOW       = 'rewardpoints/design/big_inline_image_show';
    const XML_PATH_DESIGN_BIG_INLINE_IMAGE_SIZE       = 'rewardpoints/design/big_inline_image_size';
	const XML_PATH_CONVERSION_VALUE				      = 'rewardpoints/default/points_money';

	protected $points_current;
    protected $points_on_order = null;

    public function getQuote(){
    	$quote = Mage::getSingleton('checkout/session')->getQuote();
    	return $quote;
    }
	public function login($data)
	{
		$information = parent::login($data);
		if ($this->_getSession()->isLoggedIn() && isset($information['data'][0])) {
			$information['data'][0]['loyalty_balance'] = Mage::helper('loyalty')->getMenuBalance();
		}
		return $information;
	}
	
	public function getItemPoints(){
        $details_items_line = array();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $cartItems = $quote->getAllItems();
        $ceiled_points  = 0;
        $points = 0;
        $end = array();
        foreach ($cartItems as $item)
        {
            if ($item->getRewardpointsGathered() > 0){
                if ($item->getRewardpointsCatalogRuleText() && ($catalog_rule_details = unserialize($item->getRewardpointsCatalogRuleText())) && is_array($catalog_rule_details) && sizeof($catalog_rule_details)){
                    $catalog_rule_details_txt = '';
                    foreach($catalog_rule_details as $details){
                        $catalog_rule_details_txt .= $details;
                    }                    
                    $details_items_line[] = Mage::helper('rewardpoints/data')->__('+ %s points: %s %s', $item->getRewardpointsGathered(), $item->getName(), $catalog_rule_details_txt);
                } else {
                    $details_items_line[] = Mage::helper('rewardpoints/data')->__('+ %s points: %s', $item->getRewardpointsGathered(), $item->getName());
                } 
                $ceiled_points += $item->getRewardpointsGathered();
                $points += $item->getRewardpointsGatheredFloat();
            } else if($item->getRewardpointsCatalogRuleText() && ($catalog_rule_details = unserialize($item->getRewardpointsCatalogRuleText())) && is_array($catalog_rule_details) && sizeof($catalog_rule_details)){
                foreach($catalog_rule_details as $details){
                    $end[] = $details;
                }
            }
        }
        $point_diff = ceil($points) - $ceiled_points;
        if ($point_diff != 0){
            $details_items_line[] = Mage::helper('rewardpoints/data')->__('%s coin calculation adjustment', $point_diff);
        }
        $details_items_line = array_merge($details_items_line, $end);
        return $details_items_line;
    }

     public function getQuoteCartRuleText() {
        $details_items_line = array();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote_text_rule_point = unserialize($quote->getRewardpointsCartRuleText())){
            foreach ($quote_text_rule_point as $rule_point_text){
                $details_items_line[] = $rule_point_text;
            }
        }
        return $details_items_line;
    }

	public function showDetails(){
        return Mage::getStoreConfig('rewardpoints/default/show_details', Mage::app()->getStore()->getId());
    }
    

	public function getCart($data)
	{
		$information = parent::getCart($data);			
		$info = array();
		// Add Reward Information
		// $earningPoints = Mage::helper('rewardpoints/calculation_earning')->getTotalPointsEarning();
		// $points_currently_used = $this->getPointsCurrentlyUsed();
		$points_currently_used = Mage::helper('rewardpoints/event')->getCreditPoints();
		$couponCode = Mage::getSingleton('checkout/cart')->getQuote()->getCouponCode(); 
		$pts = Mage::helper('rewardpoints/data')->getPointsOnOrder();				

		$label='';
		if (!$this->getAutoUse()){
			if (!$this->getCustomerId()){
				if (!$this->isCouponPointsRemoved()){
					$label = Mage::helper('rewardpoints/data')->__('This shopping cart is worth %s BrioCoin.',$pts).' '.Mage::helper('rewardpoints/data')->getEquivalence($pts);
					
				}else{
					$label = Mage::helper('rewardpoints/data')->__("You are currently using a discount code. Therefore, this order will not allow point gathering.");
				}
			}else{
				$point_details = $this->getPointsInfo();
				if ($this->canUseCouponCode() && ($couponCode == "" || $couponCode == null) || !$this->canUseCouponCode()){
					if (!$points_currently_used){
						if (!$this->isCouponPointsRemoved()){
						 	$label = Mage::helper('rewardpoints/data')->__("This shopping cart is worth %s BrioCoin.", $pts).' '.Mage::helper('rewardpoints/data')->getEquivalence($pts);
						}else{
						 	$label = Mage::helper('rewardpoints/data')->__("You are currently using a discount code. Therefore, this order will not allow point gathering.");
						}

						if ($point_details['min_use'] > $point_details['customer_points']){

							die('xx');
							$label .= Mage::helper('rewardpoints/data')->__('You have %d BrioCoin available.', $point_details['customer_points']) .' '.Mage::helper('rewardpoints/data')->getEquivalence($point_details['customer_points']);
							$label .= Mage::helper('rewardpoints/data')->__('To get a discount you need at least %d BrioCoin.', $point_details['min_use']);
						}else{
							$label .= Mage::helper('rewardpoints/data')->__('Enter quantity of BrioCoin you want to use.');
							$label .= Mage::helper('rewardpoints/data')->__('You have %d BrioCoin available.', $point_details['customer_points'])
									.' '.Mage::helper('rewardpoints/data')->getEquivalence($point_details['customer_points']);
							if($this->isUsable() && $this->getCustomerPoints() > 0){
								if ($point_details['step_apply']){
									if (!$this->useSlider()){
										$info['loyalty_is_slider'] = 0;
									}else{
										$info['loyalty_is_slider'] = 1;
									}
									$info['loyalty_points'] =  $this->pointsToAddOptions($point_details['customer_points'], $point_details['step']);
								}
							}elseif($this->getCustomerPoints() > 0 && $this->getConversionValue()){
								$label .= Mage::helper('rewardpoints/data')->__('You must have at least %s coin to apply points.', $this->getMinimumBalance()); 
							}
						}
					}else{
						//Cancel points
						if (!$this->isCouponPointsRemoved()){
							$label = Mage::helper('rewardpoints/data')->__("This shopping cart is worth %s BrioCoin.", $pts) .' '. Mage::helper('rewardpoints/data')->getEquivalence($pts);
						}else{
							$label = Mage::helper('rewardpoints/data')->__("You are currently using a discount code. Therefore, this order will not allow point gathering.");
						}
						if ($points_currently_used > 0){
							$label .= Mage::helper('rewardpoints/data')->__('You are currently using %d coin of your %d BrioCoin available.', $points_currently_used, $this->getCustomerPoints()); 
						}
					}
				}
			}
		}else{			
			if (!$this->isCouponPointsRemoved()){
				$label = Mage::helper('rewardpoints/data')->__("This shopping cart is worth %s BrioCoin.", $pts).' '.Mage::helper('rewardpoints/data')->getEquivalence($pts);
			}else{
				$label = Mage::helper('rewardpoints/data')->__("You are currently using a discount code. Therefore, this order will not allow point gathering.");
			}

			if ($this->getCustomerId()){
				$customerPoints = $this->getCustomerPoints(); 
				if ($customerPoints){
					if ($points_currently_used > 0){
						$label .= Mage::helper('rewardpoints/data')->__('You are currently using %d coin of your %d BrioCoin available.', $points_currently_used, $customerPoints);
					}
				}
			}
		}
		
		if (isset($information['data'][0])) {			
			$information['data'][0]['loyalty_image'] = $this->getIllustrationImage();
			$information['data'][0]['loyalty_label'] = $label;

			if($this->showDetails()){
				$loyalty_details = $this->getItemPoints();
				$rules = $this->getQuoteCartRuleText();

				if(count($loyalty_details) && !$this->isCouponPointsRemoved()){
					$information['data'][0]['loyalty_details'] = $loyalty_details;
				}			

				if(count($rules)){
					$information['data'][0]['loyalty_rules'] = $rules;
				}
			}

			if (!Mage::getModel('customer/session')->isLoggedIn() && 
				Mage::getStoreConfig('rewardpoints/default/show_login', Mage::app()->getStore()->getId())){
				$info['loyalty_login'] = 1;
				$info['loyalty_login_content'] = Mage::helper('rewardpoints/data')->__('Please Log In in order to redeem points.');
			}else{
				$information['data'][0]['loyalty_is_slider'] = $info['loyalty_is_slider'];
				$information['data'][0]['loyalty_points'] = $info['loyalty_points'];
			}
			return $information;
		}

		if ($this->canUseCouponCode() && ($points_currently_used < 1 || $points_currently_used == null) || !$this->canUseCouponCode()){			
			return $information;
		}		
	}

	 public function getIllustrationImage(){
        $img = '';
        $img_size = Mage::getStoreConfig(self::XML_PATH_DESIGN_BIG_INLINE_IMAGE_SIZE, Mage::app()->getStore()->getId());
        if (Mage::getStoreConfig(self::XML_PATH_DESIGN_BIG_INLINE_IMAGE_SHOW, Mage::app()->getStore()->getId()) && $img_size){
            $img = Mage::helper('rewardpoints/data')->getResizedUrl("j2t_image_big.png", $img_size, $img_size);            
        }
        return $img;
    }

    public function getAutoUse(){
        return Mage::getStoreConfig('rewardpoints/default/auto_use', Mage::app()->getStore()->getId());
    }

    public function getCustomerId() {
        return Mage::getModel('customer/session')->getCustomerId();
    }

     public function isCouponPointsRemoved(){
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        return ($quote->getCouponCode() && Mage::getStoreConfig('rewardpoints/default/disable_points_coupon', Mage::app()->getStore()->getId()));
    }

    public function getPointsInfo() {
        $customerId = Mage::getModel('customer/session')->getCustomerId();
        $reward_model = Mage::getModel('rewardpoints/stats');
        $store_id = Mage::app()->getStore()->getId();
        
        $customerPoints = $this->getCustomerPoints();
        
        //points required to get 1 €
        $points_money = Mage::getStoreConfig('rewardpoints/default/points_money', Mage::app()->getStore()->getId());
        //step to reach to get discount
        $step = Mage::getStoreConfig('rewardpoints/default/step_value', Mage::app()->getStore()->getId());
        //check if step needs to apply
        $step_apply = Mage::getStoreConfig('rewardpoints/default/step_apply', Mage::app()->getStore()->getId());
        $full_use = Mage::getStoreConfig('rewardpoints/default/full_use', Mage::app()->getStore()->getId());

        $order_details = $this->getQuote()->getSubtotal();
        
        $min_use = Mage::getStoreConfig('rewardpoints/default/min_use', Mage::app()->getStore()->getId());
        

        /*if (Mage::getStoreConfig('rewardpoints/default/process_tax', Mage::app()->getStore()->getId()) == 1){
            $order_details = $this->getQuote()->getSubtotalInclTax();
        }*/
        //$order_details = Mage::getModel('rewardpoints/discount')->getCartAmount();
        //J2T MOD. getCartAmount
        $order_details = Mage::getModel('rewardpoints/discount')->getCartAmount($this->getQuote());
        $cart_amount = Mage::helper('rewardpoints/data')->processMathValue($order_details);
        $max_use = min(Mage::helper('rewardpoints/data')->convertMoneyToPoints($cart_amount), $customerPoints);
        return array('min_use' => $min_use, 'customer_points' => $customerPoints, 'points_money' => $points_money, 'step' => $step, 'step_apply' => $step_apply, 'full_use' => $full_use, 'max_use' => $max_use);
    }

    public function canUseCouponCode(){
        return Mage::getStoreConfig('rewardpoints/default/coupon_codes', Mage::app()->getStore()->getId());
    }

    public function getCustomerPoints() {
        
        if ($this->points_current){
            return $this->points_current;
        }
        
        $customerId = Mage::getModel('customer/session')->getCustomerId();
        $store_id = Mage::app()->getStore()->getId();        
        
        if (Mage::getStoreConfig('rewardpoints/default/flatstats', $store_id)){
            $reward_flat_model = Mage::getModel('rewardpoints/flatstats');
            $this->points_current = $reward_flat_model->collectPointsCurrent($customerId, $store_id);
            return $this->points_current;
        }
        
        $reward_model = Mage::getModel('rewardpoints/stats');    
        
        $customerPoints = $reward_model->getPointsCurrent($customerId, $store_id);
        if (Mage::getStoreConfig('rewardpoints/default/allow_direct_usage', Mage::app()->getStore()->getId())){
            $customerPoints += $this->getPointsOnOrder();
        }
        
        $this->points_current = $customerPoints;
        
        return $this->points_current;
    }

    public function isUsable() {
		if (!$this->getConversionValue()){
			return false;
		}
        $isUsable = false;
        $minimumBalance = $this->getMinimumBalance();
        $currentBalance = $this->getCustomerPoints();
        if($currentBalance >= $minimumBalance) {
            $isUsable = true;
        }
        return $isUsable;
    }

    public function getMinimumBalance() {
        $minimumBalance = Mage::getStoreConfig('rewardpoints/default/min_use', Mage::app()->getStore()->getId());
        return $minimumBalance;
    }

    public function getConversionValue(){
		return Mage::getStoreConfig(self::XML_PATH_CONVERSION_VALUE, Mage::app()->getStore()->getId());
	}

	public function useSlider(){
        return Mage::getStoreConfig('rewardpoints/default/step_slide', Mage::app()->getStore()->getId());
    }

    public function pointsToAddOptions($customer_points, $step, $slider = false){
        $toHtml = '';
        $toHtmlArr = array();
        $creditToBeAdded = 0;
        //points required to get 1 €
        $points_money = Mage::getStoreConfig('rewardpoints/default/points_money', Mage::app()->getStore()->getId());
        $max_points_tobe_used = Mage::getStoreConfig('rewardpoints/default/max_point_used_order', Mage::app()->getStore()->getId());
        $max_order_points = Mage::helper('rewardpoints')->percentPointMax();
        $max_points_tobe_used = (($max_points_tobe_used == 0 || $max_order_points < $max_points_tobe_used) && $max_order_points > 0) ? $max_order_points : $max_points_tobe_used;
        
        $step_multiplier = Mage::getStoreConfig('rewardpoints/default/step_multiplier', Mage::app()->getStore()->getId());
        
        if (Mage::getStoreConfig('rewardpoints/default/process_rate', Mage::app()->getStore()->getId())){
            $order_details = $this->getQuote()->getBaseGrandTotal();
            $cart_amount = Mage::helper('rewardpoints/data')->convertBaseMoneyToPoints($order_details); 
            //$toHtml .= "<option>$cart_amount</option>";
        } else {
            $order_details = $this->getQuote()->getGrandTotal();
            $cart_amount = Mage::helper('rewardpoints/data')->convertMoneyToPoints($order_details);
        }        
        $customer_points_origin = $customer_points;
        
        while ($customer_points > 0){
            
            //$creditToBeAdded += $step;            
            $creditToBeAdded = ($creditToBeAdded > 0 && $step_multiplier > 1) ? ($creditToBeAdded*$step_multiplier) : ($creditToBeAdded+$step);  
            $customer_points-=$step;            
            //$toHtml .= "<option>$cart_amount < $creditToBeAdded</option>";
            
            if ($creditToBeAdded > $customer_points_origin || $cart_amount < $creditToBeAdded || ($max_points_tobe_used != 0 && $max_points_tobe_used < $creditToBeAdded)){
                //$toHtml .= "<option>$cart_amount < $creditToBeAdded</option>";
                break;
            }
            //check if credits always lower than total cart amount            
            $toHtmlArr[] = array(
            		'value' => $creditToBeAdded,
            		'label' => Mage::helper('rewardpoints/data')->__("%d BrioCoin",$creditToBeAdded)
            	);
        }        
        return $toHtmlArr;
    }
}
