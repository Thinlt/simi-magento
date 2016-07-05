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
 * Simirewardpoints Checkout on Backend Controller
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Adminhtml_Reward_CheckoutController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Change spending point used for admin create order Page
     */
    public function changePointAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setData('use_point', true);
        $session->setRewardSalesRules(array(
            'rule_id'   => $this->getRequest()->getParam('reward_sales_rule'),
            'use_point' => $this->getRequest()->getParam('reward_sales_point'),
        ));
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array()));
    }
    
    /**
     * Check using spending point for admin create order Page
     */
    public function checkboxRuleAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setData('use_point', true);
        $rewardCheckedRules = $session->getRewardCheckedRules();
        if (!is_array($rewardCheckedRules)) $rewardCheckedRules = array();
        if ($ruleId = $this->getRequest()->getParam('rule_id')) {
            if ($this->getRequest()->getParam('is_used')) {
                $rewardCheckedRules[$ruleId] = array(
                    'rule_id'   => $ruleId,
                    'use_point' => null,
                );
            } elseif (isset($rewardCheckedRules[$ruleId])) {
                unset($rewardCheckedRules[$ruleId]);
            }
            $session->setRewardCheckedRules($rewardCheckedRules);
        }
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simirewardpoints');
    }
}
