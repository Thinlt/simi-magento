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
 * Simirewardpoints Transaction Edit Block
 * 
 * @category     Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Adminhtml_Transaction_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'simirewardpoints';
        $this->_controller = 'adminhtml_transaction';
        $this->_removeButton('delete');
        
        $transaction = Mage::registry('transaction_data');
        if ($transaction && $transaction->getId()) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
            if ($transaction->getPointAmount() <= 0) {
                return ;
            }
            $this->_formScripts[] = "
                function confirmSetLocation(url) {
                    if (confirm('{$this->jsQuoteEscape(
                        $this->__('This action can not be restored. Are you sure?')
                    )}')) {
                        setLocation(url);
                    }
                }
            ";
            
            if ($transaction->getStatus() <= Simi_Simirewardpoints_Model_Transaction::STATUS_COMPLETED
                && $transaction->getExpirationDate()
                && strtotime($transaction->getExpirationDate()) < time()
                && $transaction->getPointAmount() > $transaction->getPointUsed()
            ) {
                $this->_addButton('expire_transaction', array(
                    'label'     => Mage::helper('simirewardpoints')->__('Expire'),
                    'onclick'   => "confirmSetLocation('{$this->getUrl('*/*/expire', array(
                            'id' => $transaction->getId()
                        ))}')",
                    'class'     => 'delete',
                ));
            }
            if ($transaction->getStatus() < Simi_Simirewardpoints_Model_Transaction::STATUS_COMPLETED) {
                $this->_addButton('cancel_transaction', array(
                    'label'     => Mage::helper('simirewardpoints')->__('Cancel'),
                    'onclick'   => "confirmSetLocation('{$this->getUrl('*/*/cancel', array(
                            'id' => $transaction->getId()
                        ))}')",
                    'class'     => 'delete',
                ));
                $this->_addButton('complete_transaction', array(
                    'label'     => Mage::helper('simirewardpoints')->__('Complete'),
                    'onclick'   => "confirmSetLocation('{$this->getUrl('*/*/complete', array(
                            'id' => $transaction->getId()
                        ))}')",
                    'class'     => 'save',
                ));
                return ;
            }
            $rewardAccount = $transaction->getRewardAccount();
            if ($transaction->getStatus() == Simi_Simirewardpoints_Model_Transaction::STATUS_COMPLETED
                && $transaction->getPointAmount() <= $rewardAccount->getPointBalance()
            ) {
                $this->_addButton('cancel_transaction', array(
                    'label'     => Mage::helper('simirewardpoints')->__('Cancel'),
                    'onclick'   => "confirmSetLocation('{$this->getUrl('*/*/cancel', array(
                            'id' => $transaction->getId()
                        ))}')",
                    'class'     => 'delete',
                ));
            }
            return ;
        }

        $this->_updateButton('save', 'label', Mage::helper('simirewardpoints')->__('Save Transaction'));
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('simirewardpoints')->__('Save And Continue View'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('transaction_data') && Mage::registry('transaction_data')->getId()) {
            return Mage::helper('simirewardpoints')->__("Transaction #%s", Mage::registry('transaction_data')->getId());
        }
        return Mage::helper('simirewardpoints')->__('Add Transaction');
    }
}
