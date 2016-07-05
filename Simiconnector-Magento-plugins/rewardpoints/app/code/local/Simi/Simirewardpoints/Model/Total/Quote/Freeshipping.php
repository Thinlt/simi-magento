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
 * @package     Simi_Simirewardpoints
 * @copyright   Copyright (c) 2012 Simi (http://www.simi.com/)
 * @license     http://www.simi.com/license-agreement.html
 */

/**
 * Simirewardpoints Freeshipping for order Model
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Model_Total_Quote_Freeshipping extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * set freeshipping for spent point order
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Simi_Simirewardpoints_Model_Total_Quote_Freeshipping
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        /** @var $helper Simi_Simirewardpoints_Helper_Calculation_Spending */
        $helper = Mage::helper('simirewardpoints/calculation_spending');
        
        if (!$helper->getTotalPointSpent()) {
            return $this;
        }
        
        $isFreeShipping = Mage::getStoreConfig(
            Simi_Simirewardpoints_Helper_Calculation_Spending::XML_PATH_FREE_SHIPPING,
            $address->getQuote()->getStoreId()
        );
        if (!$isFreeShipping) {
            return $this;
        }
        
        $address->setFreeShipping(true);
        Mage::dispatchEvent('simirewardpoints_collect_total_freeshipping', array(
            'address'   => $address
        ));
        
        return $this;
    }
    
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
}
