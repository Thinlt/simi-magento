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
 * Loyalty Helper
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @author      Magestore Developer
 */
class Magestore_Loyalty_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_SHOW_PRODUCT    = 'rewardpoints/loyalty/product';
	const XML_PATH_SHOW_CART       = 'rewardpoints/loyalty/cart';
	
	/*
    public function getImage($store = null)
    {s
    	if ($imgPath = Mage::getStoreConfig(Magestore_RewardPoints_Helper_Point::XML_PATH_POINT_IMAGE, $store)) {
            return Mage::getBaseUrl('media') . 'rewardpoints/' . $imgPath;
        }
        return Mage::getDesign()->getSkinUrl('images/rewardpoints/point.png');
    }
    */
	
	public function isShowOnProduct($store = null)
	{
		return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_PRODUCT, $store);
	}
	
	public function getMenuBalance()
	{
		return 1;
		// $helper = Mage::helper('rewardpoints/customer');
		// $pointAmount = $helper->getBalance();
		// if ($pointAmount > 0) {
		// 	$rate = Mage::getModel('rewardpoints/rate')->getRate(Magestore_RewardPoints_Model_Rate::POINT_TO_MONEY);
		// 	if ($rate && $rate->getId()) {
		// 		$baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
		// 		return Mage::app()->getStore()->convertPrice($baseAmount, true, false);
		// 	}
		// }
		// return $helper->getBalanceFormated();
	}
	
	public function cardConfig($field, $store = null)
	{
		return Mage::getStoreConfig('rewardpoints/passbook/' . $field, $store);
	}

	public function getTypeOfPoint($_point, $referral_id = null)
    {
        $order_id = $_point->getOrderId();
        $referral_id = $_point->getRewardpointsReferralId();
        $quote_id = $_point->getQuoteId();
        $description = ($_point->getRewardpointsDescription()) ? ' - '.$_point->getRewardpointsDescription() : '';
        $description_dyn = ($_point->getRewardpointsDescription()) ? $this->__($_point->getRewardpointsDescription()) : $this->__('Event Points');
        
        $status_field = Mage::getStoreConfig('rewardpoints/default/status_used', Mage::app()->getStore()->getId());

        $toHtml = '';
        if ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_REFERRAL_REGISTRATION){
            //rewardpoints_linker
            $model = Mage::getModel('customer/customer')->load($_point->getRewardpointsLinker());
            if ($model->getName()){
                $toHtml .= $this->__('Referral registration points (%s)', $model->getName());
            } else {
                $toHtml .= $this->__('Referral registration points');
            }
        } else if($referral_id){
            $referrer = Mage::getModel('rewardpoints/referral')->load($referral_id);
            $model = Mage::getModel('customer/customer')->load($_point->getRewardpointsLinker());
            //rewardpoints_referral_parent_id
            //rewardpoints_referral_child_id
            if ($referrer->getRewardpointsReferralParentId() && Mage::getSingleton('customer/session')->getCustomer() 
                    && is_object(Mage::getSingleton('customer/session')->getCustomer()) 
                    && $referrer->getRewardpointsReferralParentId() != Mage::getSingleton('customer/session')->getCustomer()->getId()
                    && ($customer_model = Mage::getModel('customer/customer')->load($referrer->getRewardpointsReferralParentId()))){
                $toHtml .= $this->__('Referral points (%s)',$customer_model->getName());
            } else if ($referrer->getRewardpointsReferralParentId() && Mage::getSingleton('customer/session')->getCustomer() 
                    && is_object(Mage::getSingleton('customer/session')->getCustomer()) 
                    && $model->getRewardpointsReferralChildId() != Mage::getSingleton('customer/session')->getCustomer()->getId()
                    && ($customer_model = Mage::getModel('customer/customer')->load($referrer->getRewardpointsReferralChildId()))){
                $toHtml .= $this->__('Referral points (%s)',$customer_model->getName());
            } else {
                $toHtml .= $this->__('Referral points (%s)',$referrer->getRewardpointsReferralEmail());
            }
            
            $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
            //$toHtml .=  '<div class="j2t-in-txt">'.$this->__('Referral order state: %s',$this->__($order->getData($status_field))).'</div>';
            $toHtml .=  $this->__('Referral order (#%s) state: %s', $order_id, $this->__($order->getData($status_field)));
        } elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_REVIEW){
            $toHtml .= $this->__('Review points').'</div>';
        } elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_DYN) {
            $toHtml .= $description_dyn;
        } elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_NEWSLETTER){
            $toHtml .= $this->__('Newsletter points');
        } elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_POLL){
            $toHtml .= $this->__('Poll participation points');
        } elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_TAG){
            $toHtml .= $this->__('Tag points');
        } /*elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_FIRST_ORDER){
			$toHtml .= '<div class="j2t-in-title">'.$this->__('First order points').'</div>';
		}*/
        elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_GP){
            if ($_point->getRewardpointsLinker()){
                $extra_relation = "";
                $product = Mage::getModel('catalog/product')->load($_point->getRewardpointsLinker());
                if ($product_name = Mage::helper('catalog/output')->productAttribute($product, $product->getName(), 'name')){
                    $extra_relation = "<div>".$this->__('Related to product: %s', $product_name)."</div>";
                }
            }
            $toHtml .= $this->__('Google Plus points').$extra_relation;
        } elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_FB){
            if ($_point->getRewardpointsLinker()){
                $extra_relation = "";
                $product = Mage::getModel('catalog/product')->load($_point->getRewardpointsLinker());
                if ($product_name = Mage::helper('catalog/output')->productAttribute($product, $product->getName(), 'name')){
                    $extra_relation = $this->__('Related to product: %s', $product_name);
                }
            }
            $toHtml .= $this->__('Facebook Like points').$extra_relation;
        } elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_PIN){
            if ($_point->getRewardpointsLinker()){
                $extra_relation = "";
                $product = Mage::getModel('catalog/product')->load($_point->getRewardpointsLinker());
                if ($product_name = Mage::helper('catalog/output')->productAttribute($product, $product->getName(), 'name')){
                    $extra_relation = $this->__('Related to product: %s', $product_name);
                }
            }
            $toHtml .= $this->__('Pinterest points').$extra_relation;
        } elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_TT){
            if ($_point->getRewardpointsLinker()){
                $extra_relation = "";
                $product = Mage::getModel('catalog/product')->load($_point->getRewardpointsLinker());
                
                if ($product_name = Mage::helper('catalog/output')->productAttribute($product, $product->getName(), 'name')){
                    $extra_relation = $this->__('Related to product: %s', $product_name);
                }
            }
            $toHtml .= $this->__('Twitter points').$extra_relation;
        } elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_REQUIRED){
            $current_order = Mage::getModel('sales/order')->loadByAttribute('quote_id', $quote_id);
            $points_txt = $this->__('Points used on products for order %s', $current_order->getIncrementId());
            $toHtml .= $points_txt;
        } elseif ($order_id == Rewardpoints_Model_Stats::TYPE_POINTS_BIRTHDAY){
            if (isset($points_name[$order_id])){
                $toHtml .= $points_name[$order_id];
            } else {
                $toHtml .= $this->__('Birthday points');
            }
        }
        elseif ($order_id < 0){
            $points_name = array(Rewardpoints_Model_Stats::TYPE_POINTS_REVIEW => $this->__('Review points'), Rewardpoints_Model_Stats::TYPE_POINTS_ADMIN => $this->__('Store input %s', $description), Rewardpoints_Model_Stats::TYPE_POINTS_REGISTRATION => $this->__('Registration points'));
            if (isset($points_name[$order_id])){
                $toHtml .= $points_name[$order_id];
            } else {
                $toHtml .= $this->__('Gift');
            }
            //$toHtml .= '<div class="j2t-in-title">'.$this->__('Gift').'</div>';
        } else {
			$desc = ($_point->getRewardpointsDescription()) ? $_point->getRewardpointsDescription() : '';
			
			if ($_point->getRewardpointsFirstorder()){
				$toHtml .= $this->__('First Order Points: %s', $order_id);
			} else {
				$toHtml .= $this->__('Order: %s', $order_id);
			}
			
            $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
            $toHtml .= $this->__('Order state: %s',$this->__($order->getData($status_field)));
			$toHtml .= $desc;
        }        
        if (Mage::getConfig()->getModuleConfig('J2t_Rewardshare')->is('active', 'true')){
            if ($order_id == J2t_Rewardshare_Model_Stats::TYPE_POINTS_SHARE){
                $toHtml = Mage::helper('j2trewardshare')->__('Gift (shared points)');
            }
        } 
        return $toHtml;
    }

    //Max
    protected function _getMinimalBundleOptionsPoint($product, $noCeil, $from_list, $onlyUnicMandatory = false, $customer_group_id = null) {
        if (version_compare(Mage::getVersion(), '1.8.0', '<')){
            $optionCollection = $product->getTypeInstance()->getOptionsCollection();
            $selectionCollection = $product->getTypeInstance()->getSelectionsCollection($product->getTypeInstance()->getOptionsIds());
            $options = $optionCollection->appendSelections($selectionCollection);
        } else {
            $options = $product->getTypeInstance()->getOptions($product);
        } 
	//$options = $product->getTypeInstance()->getOptions($product);
        $minimalPrice = 0;
        $minimalPriceWithTax = 0;
        $hasRequiredOptions = false;
        if ($options) {
            foreach ($options as $option) {
                if ($option->getRequired()) {
                    $hasRequiredOptions = true;
                }
            }
        }
        
        
        $selectionMinimalPoints = array();
        $selectionMinimalPointsWithTax = array();

        
        if (!$options) {
            return $minimalPrice;
        }
        
        $isPriceFixedType = ($product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED);
        
        $min_acc = 0;
        $max_acc = 0;

        foreach ($options as $option) {
            /* @var $option Mage_Bundle_Model_Option */
            $selections = $option->getSelections();
            if ($selections){
                $current_val = 0;
                $current_vals = array();
                foreach ($selections as $selection) {
                    /* @var $selection Mage_Bundle_Model_Selection */
                    if (!$selection->isSalable()) {
                        continue;
                    }
                    //$item = $isPriceFixedType ? $product : $selection;
                    //$item = $selection;
                    $subprice = $product->getPriceModel()->getSelectionPreFinalPrice($product, $selection, 1);
                    //$subprice = $selection->getPrice();
                    
                    //echo "{$selection->getId()} : $subprice // ";
                    
                    $tierprice_incl_tax = Mage::helper('tax')->getPrice($product, $subprice, true);
                    $tierprice_excl_tax = Mage::helper('tax')->getPrice($product, $subprice);
                                    
                    $current_point = $this->getProductPoints($selection, $noCeil, $from_list, false, $tierprice_incl_tax, $tierprice_excl_tax, $customer_group_id);
                    
                    //$current_point = $this->getProductPoints($item, $noCeil, $from_list);
                    
                    $current_vals[] = $current_point;
                }
               
		if ($option->getRequired() && !$onlyUnicMandatory || ($option->getRequired() && $onlyUnicMandatory && sizeof($selections) == 1)){ 
                //if ($option->getRequired()){
                    $min_acc += min($current_vals);
                }
                $max_acc += max($current_vals);
            }
        }
        
        return array($min_acc, $max_acc);
    }

    public function getEquivalence($points, $points_max = 0){
        $equivalence = '';
        $points = (int)$points;
        //if ($points > 0){
            if (Mage::getStoreConfig('rewardpoints/default/point_equivalence', Mage::app()->getStore()->getId())){
                $formattedPrice = Mage::helper('core')->currency($this->convertPointsToMoneyEquivalence(floor($points)), true, false);
                if ($points_max){
                    $formattedMaxPrice = Mage::helper('core')->currency($this->convertPointsToMoneyEquivalence(floor($points_max)), true, false);
                    $equivalence = ' '.Mage::helper('rewardpoints')->__("%d points = %s and %d points = %s.", $points, $formattedPrice, $points_max, $formattedMaxPrice);
                } else {
                    $equivalence = ' <span class="j2t-point-equivalence">'.Mage::helper('rewardpoints')->__("%d points = %s.", $points, $formattedPrice);
                }
            }
        //}
        
        return $equivalence;
    }

    public function getPointsOnProductPages($product){
    	if (!Mage::helper('rewardpoints')->isModuleActive()){
            return true;
        }

        $point_no_ceil = Mage::helper('rewardpoints/data')->getProductPoints($product, true, true);
        $points = $point_no_ceil; 
		$img = '';
		$from_list = true;

		$img_size = Mage::getStoreConfig(Rewardpoints_Helper_Data::XML_PATH_DESIGN_SMALL_INLINE_IMAGE_SIZE, Mage::app()->getStore()->getId());
        if (Mage::getStoreConfig(Rewardpoints_Helper_Data::XML_PATH_DESIGN_SMALL_INLINE_IMAGE_SHOW, Mage::app()->getStore()->getId()) && $img_size){
            $img = Mage::helper('rewardpoints/data')->getResizedUrl('j2t_image_small.png', $img_size, $img_size);
        }

        $points = ceil($points);

        if (!$points && $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED){
            //list all products in grouped item
            $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            $points = 0;
            foreach ($associatedProducts as $single_product){
                $points += Mage::helper('rewardpoints/data')->getProductPoints($product, true, true);
            }
        }

        if ($points && $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED){
            $extraPointDetails = array();
            if($cms_page = Mage::getStoreConfig('rewardpoints/product_page/cms_page')){
                $extraPointDetails['url'] = Mage::getUrl($cms_page);
                $extraPointDetails['title'] = Mage::helper('rewardpoints')->__('Find more about this!');                
            }
            
            if ($from_list){
            	return array(
            			'image' => $img,
            			'content' => Mage::helper('rewardpoints')->__("With this product, you earn up to %d BrioCoin.", $points) . $this->getEquivalence($points),
            			'extra_point' => $extraPointDetails,
            		);
            }else{
            	return array(
            			'image' => $img,
            			'content' => Mage::helper('rewardpoints')->__("With this product, you earn up to BrioCoin.", $points) . $this->getEquivalence($points),
            			'extra_point' => $extraPointDetails,
            		);            	 
            }
        }
        if ($points && $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE && $product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED){
            //$return = '<p class="j2t-loyalty-points inline-points">'.$img. Mage::helper('rewardpoints')->__("With this product, you earn <span id='j2t-pts'>%d</span> BrioCoin.", $points) . $this->getEquivalence($points) . '</p>';
            list($points_min, $points_max) = $this->_getMinimalBundleOptionsPoint($product, true, true);
            
            $points_min = ceil($points_min+$point_no_ceil);
            $points_max = ceil($points_max+$point_no_ceil);
            
            $extraPointDetails = array();
            if($cms_page = Mage::getStoreConfig('rewardpoints/product_page/cms_page')){
                $extraPointDetails['url'] = Mage::getUrl($cms_page);
                $extraPointDetails['title'] = Mage::helper('rewardpoints')->__('Find more about this!');                
            }            
            
            if ($from_list && $points_min == $points_max && $points_min == 0){
                return array();
            } else if ($from_list && $points_min == $points_max){
            	return array(
            			'image' => $img,
            			'content' => Mage::helper('rewardpoints')->__("With this product, you earn up to %d BrioCoin.", $points_min) . $this->getEquivalence($points_min),
            			'extra_point' => $extraPointDetails,
            		);                
            } else if ($from_list){ 
            	return array(
            			'image' => $img,
            			'content' => Mage::helper('rewardpoints')->__("With this product, you earn from %d to %d BrioCoin.", $points_min, $points_max) . $this->getEquivalence($points_min, $points_max),
            			'extra_point' => $extraPointDetails,
            		);                    
            } else {
                return array(
            			'image' => $img,
            			'content' => Mage::helper('rewardpoints')->__("With this product, you earn %d BrioCoin.", $points_min) . $this->getEquivalence($points_min),
            			'extra_point' => $extraPointDetails,
            		);                   
            }
        }else if ($points){            
            $extraPointDetails = array();
            $content = '';
            if (Mage::registry('current_product') 
                    && Mage::registry('current_product')->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE 
                    && Mage::getStoreConfig('rewardpoints/default/show_details', Mage::app()->getStore()->getId())){
                $point_details = unserialize(Mage::registry('current_product')->getPointDetails());
                if ($point_details && is_array($point_details) && sizeof($point_details)){
                    foreach ($point_details as $point_detail){
                        if ($point_detail && is_array($point_detail) && sizeof($point_detail)){
                            foreach ($point_detail as $details){
                                $extraPointDetails['title'] .= $details;
                            }
                        }
                    }
                    
                    $point_diff = $points - ceil(Mage::registry('current_product')->getPointRuleTotal());
                    if ($point_diff != 0){
                        $extraPointDetails['title'] .= Mage::helper('rewardpoints')->__('%s point(s) calculation adjustment', $point_diff);
                    }
                }
            }                        
            if($cms_page = Mage::getStoreConfig('rewardpoints/product_page/cms_page')){
            	$extraPointDetails['title'] = Mage::helper('rewardpoints')->__('Find more about this!');
            	$extraPointDetails['url'] = Mage::getUrl($cms_page);                
            }

            return array(
            		'image' => $img,
        			'content' => Mage::helper('rewardpoints')->__("With this product, you earn %s BrioCoin.", $points),
        			'extra_point' => $extraPointDetails,
            	);
        }else if($from_list) {
            //try to get from price
            $attribute_restriction = Mage::getStoreConfig('rewardpoints/default/process_restriction', Mage::app()->getStore()->getId());
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED && !$attribute_restriction) {
                $product_default_points = Mage::helper('rewardpoints/data')->getDefaultProductPoints($product, Mage::app()->getStore()->getId(), true, false);
                $catalog_points = Mage::getModel('rewardpoints/catalogpointrules')->getAllCatalogRulePointsGathered($product, $product_default_points);
                
                if ($catalog_points !== false){
                    $_associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
                    $product_points = array();
                    foreach ($_associatedProducts as $curent_asso_product){
                        $product_points[] = Mage::helper('rewardpoints/data')->getProductPoints($curent_asso_product, false, true, false, null, null);
                    }
                    if (sizeof($product_points)){
                        $points_min = ceil(min($product_points));
                        return array(
		            		'image' => $img,
		        			'content' => Mage::helper('rewardpoints')->__("With this product, you earn %d BrioCoin.", $points_min) . $this->getEquivalence($points_min),
		        			'extra_point' => $extraPointDetails,
		            	);                        
                    }
                }
            }
            else if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE && !$attribute_restriction){
                $product_default_points = Mage::helper('rewardpoints/data')->getDefaultProductPoints($product, Mage::app()->getStore()->getId(), true, false);
                $catalog_points = Mage::getModel('rewardpoints/catalogpointrules')->getAllCatalogRulePointsGathered($product, $product_default_points);
               		
                if ($catalog_points !== false || $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){                
                    if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
                        list($points_min, $points_max) = $this->_getMinimalBundleOptionsPoint($product, false, true, false);
                    } else { 
			             $_priceModel  = $product->getPriceModel();      	               
                    	if (Mage::getStoreConfig('rewardpoints/default/exclude_tax', Mage::app()->getStore()->getId())){
                            if (version_compare(Mage::getVersion(), '1.5.0', '<')){
                        	list($_minimalPrice, $_maximalPrice) = $_priceModel->getPrices($product);
                            } else {
                                list($_minimalPrice, $_maximalPrice) = $_priceModel->getTotalPrices($product, null, null, false);
                            }
                        } else {
                            if (version_compare(Mage::getVersion(), '1.5.0', '<')){
                                list($_minimalPrice, $_maximalPrice) = $_priceModel->getPrices($product);
                                $_minimalPrice = Mage::helper('tax')->getPrice($product, $_minimalPrice);
                                $_maximalPrice = Mage::helper('tax')->getPrice($product, $_maximalPrice, true);
                            } else {
                                list($_minimalPrice, $_maximalPrice) = $_priceModel->getTotalPrices($product, null, true, false);
                            }
                        }                       
                        $points_min = ceil($this->convertProductMoneyToPoints($_minimalPrice));
                        $points_max = ceil($this->convertProductMoneyToPoints($_maximalPrice));
                    } 
                    if ($from_list && $points_min == 0 && $points_max == 0) {
                    	return array(
		            		'image' => $img,
		        			'content' => Mage::helper('rewardpoints')->__("With this product, earned points will depend on product configuration."),
		        			'extra_point' => array(),
		            	);                         
                    } else if ($from_list && $points_min == $points_max){
                    	return array(
		            		'image' => $img,
		        			'content' => Mage::helper('rewardpoints')->__("With this product, you earn %d BrioCoin.", $points_min) . $this->getEquivalence($points_min),
		        			'extra_point' => array(),
		            	);                         
                    } else if ($from_list){
                    	return array(
		            		'image' => $img,
		        			'content' => Mage::helper('rewardpoints')->__("With this product, you earn from %d to %d BrioCoin.", $points_min, $points_max) . $this->getEquivalence($points_min, $points_max),
		        			'extra_point' => array(),
		            	);                                           
                    } else {
                    	return array(
		            		'image' => $img,
		        			'content' => Mage::helper('rewardpoints')->__("With this product, you earn %d BrioCoin.", $points_min) . $this->getEquivalence($points_min),
		        			'extra_point' => array(),
		            	);                                                                   
                    }
                }
            } 
        }  

        $points_min = 0;
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
            list($points_min, $points_max) = $this->_getMinimalBundleOptionsPoint($product, true, $from_list, false);
        }
        $points = ceil($points+$points_min); 	
        return array(
            		'image' => $img,
        			'content' => Mage::helper('rewardpoints')->__("With this product, you earn %d BrioCoin.", $points) . $this->getEquivalence($points),
        			'extra_point' => array(),
            	);        
    }
}
