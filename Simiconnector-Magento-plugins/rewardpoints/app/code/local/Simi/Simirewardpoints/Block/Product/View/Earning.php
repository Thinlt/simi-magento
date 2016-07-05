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
 * Simirewardpoints Show Earning Point on Mini Cart Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Product_View_Earning extends Simi_Simirewardpoints_Block_Template
{
    /**
     * Check store is enable for display on minicart sidebar
     * 
     * @return boolean
     */
    public function enableDisplay()
    {
        $enableDisplay = Mage::helper('simirewardpoints/point')->showOnProduct();
        $container = new Varien_Object(array(
            'enable_display' => $enableDisplay
        ));
        Mage::dispatchEvent('simirewardpoints_block_show_earning_on_product', array(
            'container' => $container,
        ));
        if ($container->getEnableDisplay() && !$this->hasEarningRate() || Mage::registry('product')->getSimirewardpointsSpend()) {
            return false;
        }
        return $container->getEnableDisplay();
    }
    
    /**
     * check product can earn point by rate or not
     * 
     * @return boolean
     */
    public function hasEarningRate()
    {
        if ($product = Mage::registry('product')) {
            if (!Mage::helper('simirewardpoints/calculation_earning')->getRateEarningPoints(10000)) {
                return false;
            }
            $productPrice = $product->getPrice();
            if ($productPrice < 0.0001 && $product->getTypeId() == 'bundle') {
                $productPrice = $product->getPriceModel()->getPrices($product, 'min');
            }
            if ($productPrice > 0.0001) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * get Image (HTML) for reward points
     * 
     * @param boolean $hasAnchor
     * @return string
     */
    public function getImageHtml($hasAnchor = true)
    {
        return Mage::helper('simirewardpoints/point')->getImageHtml($hasAnchor);
    }
    
    /**
     * get plural points name
     * 
     * @return string
     */
    public function getPluralPointName()
    {
        return Mage::helper('simirewardpoints/point')->getPluralName();
    }
	public function getEarningPoints()
    {
        if ($this->hasData('earning_points')) {
            return $this->getData('earning_points');
        }
        if (Mage::registry('product') && $point=Mage::helper('simirewardpoints/calculation_earning')->getRateEarningPoints(Mage::registry('product')->getFinalPrice())) {
            $this->setData('earning_points', $point);
        }
        return $this->getData('earning_points');
    }
}
