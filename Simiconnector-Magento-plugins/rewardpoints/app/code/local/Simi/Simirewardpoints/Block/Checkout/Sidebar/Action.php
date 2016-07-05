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
class Simi_Simirewardpoints_Block_Checkout_Sidebar_Action extends Simi_Simirewardpoints_Block_Template
{
    /**
     * Check store is enable for display on minicart sidebar
     * 
     * @return type
     */
    public function enableDisplay()
    {
        return Mage::helper('simirewardpoints/point')->showOnMiniCart();
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
}
