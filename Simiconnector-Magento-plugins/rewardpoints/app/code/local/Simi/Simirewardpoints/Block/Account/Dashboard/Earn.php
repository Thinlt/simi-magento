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
 * Simirewardpoints Account Dashboard Earning Policy
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Account_Dashboard_Earn extends Simi_Simirewardpoints_Block_Template
{
    /**
     * check showing container
     * 
     * @return boolean
     */
    public function getCanShow()
    {
        $rate = $this->getEarningRate();
        if ($rate && $rate->getId()) {
            $canShow = true;
        } else {
            $canShow = false;
        }
        $container = new Varien_Object(array(
            'can_show' => $canShow
        ));
        Mage::dispatchEvent('simirewardpoints_block_dashboard_earn_can_show', array(
            'container' => $container,
        ));
        return $container->getCanShow();
    }
    
    /**
     * get earning rate
     * 
     * @return Simi_Simirewardpoints_Model_Rate
     */
    public function getEarningRate()
    {
        if (!$this->hasData('earning_rate')) {
            $this->setData('earning_rate',
                Mage::getModel('simirewardpoints/rate')->getRate(Simi_Simirewardpoints_Model_Rate::MONEY_TO_POINT)
            );
        }
        return $this->getData('earning_rate');
    }
    
    /**
     * get current money formated of rate
     * 
     * @param Simi_Simirewardpoints_Model_Rate $rate
     * @return string
     */
    public function getCurrentMoney($rate)
    {
        if ($rate && $rate->getId()) {
            $money = $rate->getMoney();
            return Mage::app()->getStore()->convertPrice($money, true);
        }
        return '';
    }
}
