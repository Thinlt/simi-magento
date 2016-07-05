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
 * Simirewardpoints Account Dashboard
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Account_Dashboard extends Simi_Simirewardpoints_Block_Template
{
    /**
     * get current balance of customer as text
     * 
     * @return string
     */
    public function getBalanceText()
    {
        return Mage::helper('simirewardpoints/customer')->getBalanceFormated();
    }
    
    /**
     * get holding balance of customer as text
     * 
     * @return int
     */
    public function getHoldingBalance()
    {
        $holdingBalance = Mage::helper('simirewardpoints/customer')->getAccount()->getHoldingBalance();
        if ($holdingBalance > 0) {
            return Mage::helper('simirewardpoints/point')->format($holdingBalance);
        }
        return '';
    }
    
    /**
     * get point money balance of customer
     * 
     * @return string
     */
    public function getPointMoney()
    {
        $pointAmount = Mage::helper('simirewardpoints/customer')->getBalance();
        if ($pointAmount > 0) {
            $rate = Mage::getModel('simirewardpoints/rate')->getRate(Simi_Simirewardpoints_Model_Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                return Mage::app()->getStore()->convertPrice($baseAmount, true);
            }
        }
        return '';
    }
}
