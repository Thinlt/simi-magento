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
 * Simirewardpoints Show Cart Total (Review about Earning/Spending Reward Points)
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Checkout_Cart_Label extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'simirewardpoints/checkout/cart/label.phtml';
    
	protected function _construct(){
        parent::_construct();
        if ($this->getRequest()->getModuleName() =='webpos') {
            $this->setTemplate('simirewardpoints/checkout/cart/webposlabel.phtml');
        } else {
			$this->setTemplate($this->_template);
		}
    }
    /**
     * check reward points system is enabled or not
     * 
     * @return boolean
     */
    public function isEnable()
    {
        return Mage::helper('simirewardpoints')->isEnable();
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
        if (Mage::helper('simirewardpoints/calculation_spending')->getTotalPointSpent() && !Mage::getStoreConfigFlag('simirewardpoints/earning/earn_when_spend',Mage::app()->getStore()->getId())) {
            return 0;
        }
        return Mage::helper('simirewardpoints/calculation_earning')->getTotalPointsEarning();
    }
}
