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
 * Action change points by admin
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Model_Action_Admin
    extends Simi_Simirewardpoints_Model_Action_Abstract
    implements Simi_Simirewardpoints_Model_Action_Interface
{
    /**
     * Calculate and return point amount that admin changed
     * 
     * @return int
     */
    public function getPointAmount()
    {
        $actionObject = $this->getData('action_object');
        if (!is_object($actionObject)) {
            return 0;
        }
        return (int)$actionObject->getPointAmount();
    }
    
    /**
     * get Label for this action, this is the reason to change 
     * customer reward points balance
     * 
     * @return string
     */
    public function getActionLabel()
    {
        return Mage::helper('simirewardpoints')->__('Changed by Admin');
    }
    
    public function getActionType()
    {
        return Simi_Simirewardpoints_Model_Transaction::ACTION_TYPE_BOTH;
    }
    
    /**
     * get Text Title for this action, used when create an transaction
     * 
     * @return string
     */
    public function getTitle()
    {
        $actionObject = $this->getData('action_object');
        if (!is_object($actionObject)) {
            return '';
        }
        return (string)$actionObject->getData('title');
    }
    
    /**
     * get HTML Title for action depend on current transaction
     * 
     * @param Simi_Simirewardpoints_Model_Transaction $transaction
     * @return string
     */
    public function getTitleHtml($transaction = null)
    {
        if (is_null($transaction)) {
            return $this->getTitle();
        }
        if (Mage::app()->getStore()->isAdmin()) {
            return '<strong>' . $transaction->getExtraContent() . ': </strong>' . $transaction->getTitle();
        }
        return $transaction->getTitle();
    }
    
    /**
     * prepare data of action to storage on transactions
     * the array that returned from function $action->getData('transaction_data')
     * will be setted to transaction model
     * 
     * @return Simi_Simirewardpoints_Model_Action_Interface
     */
    public function prepareTransaction()
    {
        $transactionData = array(
            'status'    => Simi_Simirewardpoints_Model_Transaction::STATUS_COMPLETED,
        );
        if ($user = Mage::getSingleton('admin/session')->getUser()) {
            $transactionData['extra_content'] = $user->getUsername();
        }
        $actionObject = $this->getData('action_object');
        if (is_object($actionObject) && $actionObject->getExpirationDay() && $this->getPointAmount() > 0) {
            $transactionData['expiration_date'] = $this->getExpirationDate($actionObject->getExpirationDay());
        }
        $this->setData('transaction_data', $transactionData);
        return parent::prepareTransaction();
    }
}
