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
 * Simirewardpoints Show Cart Total (Review about Earning/Spending Reward Points) on Backend
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Adminhtml_Cart_Label extends Mage_Adminhtml_Block_Sales_Order_Create_Totals_Default
{
    protected $_template = 'simirewardpoints/checkout/cart/label.phtml';
    
    /**
     * check reward points system is enabled or not
     * 
     * @return boolean
     */
    public function isEnable()
    {
        return Mage::helper('simirewardpoints')->isEnable($this->getStore());
    }
    
    /**
     * get reward points helper
     * 
     * @return Simi_Simirewardpoints_Helper_Point
     */
    public function getPointHelper()
    {
        return Mage::helper('simirewardpoints/point');
    }
    
    /**
     * get total points that customer use to spend for order
     * 
     * @return int
     */
    public function getSpendingPoint()
    {
        return Mage::helper('simirewardpoints/calculation_spending')->getTotalPointSpent();
    }
    
    /**
     * get total points that customer can earned by purchase order
     * 
     * @return int
     */
    public function getEarningPoint()
    {
        return Mage::helper('simirewardpoints/calculation_earning')->getTotalPointsEarning();
    }
}
