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
 * Simirewardpoints Settings
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Account_settings extends Simi_Simirewardpoints_Block_Template
{
    /**
     * get current reward points account
     * 
     * @return Simi_Simirewardpoints_Model_Customer
     */
    public function getRewardAccount()
    {
        $rewardAccount = Mage::helper('simirewardpoints/customer')->getAccount();
        if (!$rewardAccount->getId()) {
            $rewardAccount->setIsNotification(1)
                ->setExpireNotification(1);
        }
        return $rewardAccount;
    }
}
