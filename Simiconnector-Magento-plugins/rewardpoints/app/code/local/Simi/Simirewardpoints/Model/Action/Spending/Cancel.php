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
 * Action Cancel Spent Points for Order
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Model_Action_Spending_Cancel
    extends Simi_Simirewardpoints_Model_Action_Abstract
    implements Simi_Simirewardpoints_Model_Action_Interface
{
    /**
     * Calculate and return point amount that spent for order
     * 
     * @return int
     */
    public function getPointAmount()
    {
        $order = $this->getData('action_object');
        return (int)$order->getRefundSpentPoints();
    }
    
    /**
     * get Label for this action, this is the reason to change 
     * customer reward points balance
     * 
     * @return string
     */
    public function getActionLabel()
    {
        return Mage::helper('simirewardpoints')->__('Retrieve points spent on cancelled order');
    }
    
    public function getActionType()
    {
        return Simi_Simirewardpoints_Model_Transaction::ACTION_TYPE_SPEND;
    }
    
    /**
     * get Text Title for this action, used when create an transaction
     * 
     * @return string
     */
    public function getTitle()
    {
        $order = $this->getData('action_object');
        return Mage::helper('simirewardpoints')->__('Retrieve points spent on cancelled order #%s', $order->getIncrementId());
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
            $editUrl = Mage::getUrl('adminhtml/sales_order/view', array('order_id' => $transaction->getOrderId()));
        } else {
            $editUrl = Mage::getUrl('sales/order/view', array('order_id' => $transaction->getOrderId()));
        }
        return Mage::helper('simirewardpoints')->__(
            'Retrieve points spent on cancelled order %s',
            '<a href="' . $editUrl .'" title="'
            . Mage::helper('simirewardpoints')->__('View Order')
            . '">#' . $transaction->getOrderIncrementId() . '</a>'
        );
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
        $order = $this->getData('action_object');
        
        $transactionData = array(
            'status'    => Simi_Simirewardpoints_Model_Transaction::STATUS_COMPLETED,
            'order_id'  => $order->getId(),
            'order_increment_id'    => $order->getIncrementId(),
            'order_base_amount'     => $order->getBaseGrandTotal(),
            'order_amount'          => $order->getGrandTotal(),
            'base_discount'         => $order->getSimirewardpointsBaseDiscount(),
            'discount'              => $order->getSimirewardpointsDiscount(),
            'store_id'      => $order->getStoreId(),
        );
        
        // Set expire time for current transaction
        $expireDays = (int)Mage::getStoreConfig(
            Simi_Simirewardpoints_Helper_Calculation_Earning::XML_PATH_EARNING_EXPIRE,
            $order->getStoreId()
        );
        $transactionData['expiration_date'] = $this->getExpirationDate($expireDays);
        
        $this->setData('transaction_data', $transactionData);
        return parent::prepareTransaction();
    }
}
