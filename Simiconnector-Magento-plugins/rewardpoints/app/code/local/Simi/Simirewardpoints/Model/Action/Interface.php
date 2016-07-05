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
 * Action Interface to add action for Reward Points system
 * All action on Reward Points system need implements from this interface
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
interface Simi_Simirewardpoints_Model_Action_Interface
{
    /**
     * Calculate and return point amount that action has
     * + point amount > 0 => action will add point to customer
     * + point amount < 0 => action will reduce point from customer
     * + point amount = 0 => take no action
     * 
     * @return int
     */
    public function getPointAmount();
    
    /**
     * get Label for this action, this is the reason to change 
     * customer reward points balance
     * 
     * @return string
     */
    public function getActionLabel();
    
    /**
     * get type of this action (earning or spending / both)
     * 
     * @return int
     */
    public function getActionType();
    
    /**
     * get Text Title for this action, used when create an transaction
     * 
     * @return string
     */
    public function getTitle();
    
    /**
     * get HTML Title for action depend on current transaction
     * 
     * @param Simi_Simirewardpoints_Model_Transaction $transaction
     * @return string
     */
    public function getTitleHtml($transaction = null);
    
    /**
     * prepare data of action to storage on transactions
     * the array that returned from function $action->getData('transaction_data')
     * will be setted to transaction model
     * 
     * @return Simi_Simirewardpoints_Model_Action_Interface
     */
    public function prepareTransaction();
    
    /**
     * get action code
     * 
     * @return string
     */
    public function getCode();
    
    /**
     * set action code
     * 
     * @param string $value
     * @return Simi_Simirewardpoints_Model_Action_Interface
     */
    public function setCode($value);
    
    /**
     * get data from current Action
     * 
     * @param string $key
     * @param string|int $index
     * @return mixed
     */
    public function getData($key='', $index=null);
    
    /**
     * set data for current Action
     * 
     * @param mixed $key
     * @param mixed $value
     * @return Simi_Simirewardpoints_Model_Action_Interface
     */
    public function setData($key, $value = null);
}
