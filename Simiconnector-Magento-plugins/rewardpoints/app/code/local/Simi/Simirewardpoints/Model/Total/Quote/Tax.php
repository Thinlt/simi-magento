<?php
/**
 * Simi
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Simi.com license that is
 * available through the world-wide-web at this URL:
 * http://www.simi.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Simi
 * @package     Simi_Simi
 * @copyright   Copyright (c) 2012 Simi (http://www.simi.com/)
 * @license     http://www.simi.com/license-agreement.html
 */

/**
 * Simi Rewrite to caculate taxt for discount
 * 
 * @category    Simi
 * @package     Simi_Simi
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Model_Total_Quote_Tax extends Mage_Tax_Model_Sales_Total_Quote_Tax
{
    
    /**
     * Calculate tax for Quote (total)
     * 
     * @param type $item
     * @param type $rate
     * @param type $taxGroups
     * @return Simi_Simi_Model_Total_Quote_Tax
     */
    protected function _aggregateTaxPerRate($item, $rate, &$taxGroups) {
        $discount       = $item->getDiscountAmount();
        $baseDiscount   = $item->getBaseDiscountAmount();
        if($item->getIsPriceInclTax()){
            $item->setSimiDiscountTax($this->_calculator->calcTaxAmount($item->getSimiDiscount(), $rate, false, false));
            $item->setSimiBaseDiscountTax($this->_calculator->calcTaxAmount($item->getSimiBaseDiscount(), $rate, false, false));
        }
        $item->setDiscountAmount($discount + $item->getSimiDiscount() + $item->getSimiDiscountTax());
        $item->setBaseDiscountAmount($baseDiscount + $item->getSimiBaseDiscount() + $item->getSimiBaseDiscountTax());
        
        parent::_aggregateTaxPerRate($item, $rate, $taxGroups);
        
        $afterDiscount = (bool)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $this->_store);
        if($item->getIsPriceInclTax() && $afterDiscount){
            foreach ($this->_hiddenTaxes as $key => $taxInfoItem) {
                if (isset($taxInfoItem['item']) && $item->getId() == $taxInfoItem['item']->getId() && $taxInfoItem['value'] >= $item->getSimiDiscountTax() && $item->getSimiDiscountTax() >0) {
                    $this->_hiddenTaxes[$key]['value'] = $taxInfoItem['value'] - $item->getSimiDiscountTax();
                    $this->_hiddenTaxes[$key]['base_value'] = $taxInfoItem['base_value'] - $item->getSimiBaseDiscountTax();
                    break;
                }
            }
            //fix 1.4
            if($item->getHiddenTaxAmount()){
                $item->setHiddenTaxAmount($item->getHiddenTaxAmount() - $item->getSimiDiscountTax());
                $item->setBaseHiddenTaxAmount($item->getBaseHiddenTaxAmount() - $item->getSimiBaseDiscountTax());
            }
        }
        
        $item->setDiscountAmount($discount);
        $item->setBaseDiscountAmount($baseDiscount);
        return $this;
    }
    
    /**
     * Calculate tax for each product
     * 
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param type $rate
     * @return Simi_Simi_Model_Total_Quote_Tax
     */
    protected function _calcUnitTaxAmount(Mage_Sales_Model_Quote_Item_Abstract $item, $rate) {
        $discount       = $item->getDiscountAmount();
        $baseDiscount   = $item->getBaseDiscountAmount();
        if($item->getIsPriceInclTax()){
            $item->setSimiDiscountTax($this->_calculator->calcTaxAmount($item->getSimiDiscount(), $rate, false, false));
            $item->setSimiBaseDiscountTax($this->_calculator->calcTaxAmount($item->getSimiBaseDiscount(), $rate, false, false));
        }
        $item->setDiscountAmount($discount + $item->getSimiDiscount() + $item->getSimiDiscountTax());
        $item->setBaseDiscountAmount($baseDiscount + $item->getSimiDiscount() + $item->getSimiBaseDiscountTax());
        
        parent::_calcUnitTaxAmount($item, $rate);
        
        $afterDiscount = (bool)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $this->_store);
        if($item->getIsPriceInclTax() && $afterDiscount){
            foreach ($this->_hiddenTaxes as $key => $taxInfoItem) {
                if (isset($taxInfoItem['item']) && $item->getId() == $taxInfoItem['item']->getId() && $taxInfoItem['value'] >= $item->getSimiDiscountTax() && $item->getSimiDiscountTax() >0) {
                    $this->_hiddenTaxes[$key]['value'] = $taxInfoItem['value'] - $item->getSimiDiscountTax();
                    $this->_hiddenTaxes[$key]['base_value'] = $taxInfoItem['base_value'] - $item->getSimiBaseDiscountTax();
                    break;
                }
            }
            //fix 1.4
            if($item->getHiddenTaxAmount()){
                $item->setHiddenTaxAmount($item->getHiddenTaxAmount() - $item->getSimiDiscountTax());
                $item->setBaseHiddenTaxAmount($item->getBaseHiddenTaxAmount() - $item->getSimiBaseDiscountTax());
            }
        }
        
        $item->setDiscountAmount($discount);
        $item->setBaseDiscountAmount($baseDiscount);
        return $this;
    }
    
    /**
     * Calculate tax for each item
     * 
     * @param type $item
     * @param type $rate
     * @return Simi_Simi_Model_Total_Quote_Tax
     */
    protected function _calcRowTaxAmount($item, $rate) {
        $discount       = $item->getDiscountAmount();
        $baseDiscount   = $item->getBaseDiscountAmount();
        if($item->getIsPriceInclTax()){
            $item->setSimiDiscountTax($this->_calculator->calcTaxAmount($item->getSimiDiscount(), $rate, false, false));
            $item->setSimiBaseDiscountTax($this->_calculator->calcTaxAmount($item->getSimiBaseDiscount(), $rate, false, false));
        }
        $item->setDiscountAmount($discount + $item->getSimiDiscount() + $item->getSimiDiscountTax());
        $item->setBaseDiscountAmount($baseDiscount + $item->getSimiDiscount() + $item->getSimiBaseDiscountTax());
        
        parent::_calcRowTaxAmount($item, $rate);
        
        $afterDiscount = (bool)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $this->_store);
        if($item->getIsPriceInclTax() && $afterDiscount){
            foreach ($this->_hiddenTaxes as $key => $taxInfoItem) {
                if (isset($taxInfoItem['item']) && $item->getId() == $taxInfoItem['item']->getId() && $taxInfoItem['value'] >= $item->getSimiDiscountTax() && $item->getSimiDiscountTax() >0) {
                    $this->_hiddenTaxes[$key]['value'] = $taxInfoItem['value'] - $item->getSimiDiscountTax();
                    $this->_hiddenTaxes[$key]['base_value'] = $taxInfoItem['base_value'] - $item->getSimiBaseDiscountTax();
                    break;
                }
            }
            //fix 1.4
            if($item->getHiddenTaxAmount()){
                $item->setHiddenTaxAmount($item->getHiddenTaxAmount() - $item->getSimiDiscountTax());
                $item->setBaseHiddenTaxAmount($item->getBaseHiddenTaxAmount() - $item->getSimiBaseDiscountTax());
            }
        }
        
        $item->setDiscountAmount($discount);
        $item->setBaseDiscountAmount($baseDiscount);
        return $this;
    }
    
    /**
     * Calculate tax for shipping amount
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @param type $taxRateRequest
     */
    protected function _calculateShippingTax(Mage_Sales_Model_Quote_Address $address, $taxRateRequest) {
        $discount       = $address->getShippingDiscountAmount();
        $baseDiscount   = $address->getBaseShippingDiscountAmount();
        $taxRateRequest->setProductClassId($this->_config->getShippingTaxClass($this->_store));
        if($address->getIsShippingInclTax()){
            $address->setSimiDiscountTaxForShipping($this->_calculator->calcTaxAmount($address->getSimiDiscountForShipping(), $this->_calculator->getRate($taxRateRequest), false, false));
            $address->setSimiBaseDiscountTaxForShipping($this->_calculator->calcTaxAmount($address->getSimiBaseDiscountForShipping(), $this->_calculator->getRate($taxRateRequest), false, false));
        }
        $address->setShippingDiscountAmount($discount+$address->getSimiDiscountForShipping()+$address->getSimiDiscountTaxForShipping());
        $address->setBaseShippingDiscountAmount($baseDiscount+$address->getSimiBaseDiscountForShipping()+$address->getSimiBaseDiscountTaxForShipping());
        
        parent::_calculateShippingTax($address, $taxRateRequest);
        
        if($address->getIsShippingInclTax() && $address->getSimiDiscountTaxForShipping() > 0){
            $length = count($this->_hiddenTaxes);
            if($this->_hiddenTaxes[$length-1]['value']>0){
                $this->_hiddenTaxes[$length-1]['value'] = $this->_hiddenTaxes[$length-1]['value'] - $address->getSimiDiscountTaxForShipping();
                $this->_hiddenTaxes[$length-1]['base_value'] = $this->_hiddenTaxes[$length-1]['base_value'] - $address->getSimiBaseDiscountTaxForShipping();
            }
            
            //fix 1.4
            if($address->getShippingHiddenTaxAmount()){
                $address->setShippingHiddenTaxAmount($address->getShippingHiddenTaxAmount() - $address->getSimiDiscountTaxForShipping());
                $address->setBaseShippingHiddenTaxAmount($address->getBaseShippingHiddenTaxAmount() - $address->getSimiBaseDiscountTaxForShipping());
            }
        }
        
        $address->setShippingDiscountAmount($discount);
        $address->setBaseShippingDiscountAmount($baseDiscount);
        return $this;
    }
}
