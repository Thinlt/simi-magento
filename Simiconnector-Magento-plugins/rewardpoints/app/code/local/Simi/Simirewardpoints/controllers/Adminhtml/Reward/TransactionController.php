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
 * Simirewardpoints Adminhtml Controller
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Adminhtml_Reward_TransactionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Simi_Simirewardpoints_Adminhtml_TransactionController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('simirewardpoints/transaction')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Transactions Manager'),
                Mage::helper('adminhtml')->__('Transaction Manager')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_title($this->__('Reward Points'))
            ->_title($this->__('Transaction Manager'));
        $this->_initAction()
            ->renderLayout();
    }
    
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $transactionId     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('simirewardpoints/transaction')->load($transactionId);

        if ($model->getId() || $transactionId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('transaction_data', $model);
            
            $this->loadLayout();
            
            $this->_setActiveMenu('simirewardpoints/transaction');
            
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Transactions Manager'),
                Mage::helper('adminhtml')->__('Transaction Manager')
            );
            
            $this->_title($this->__('Reward Points'))
                ->_title($this->__('Transaction Manager'));
            if ($model->getId()) {
                $this->_title($this->__('Transaction #%s', $model->getId()));
            } else {
                $this->_title($this->__('New Transaction'));
            }
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('simirewardpoints/adminhtml_transaction_edit'))
                ->_addLeft($this->getLayout()->createBlock('simirewardpoints/adminhtml_transaction_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('simirewardpoints')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function customerAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }
    
    public function customerGridAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }
 
    /**
     * save item action
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $request = $this->getRequest();
                $customer = Mage::getModel('customer/customer')->load($request->getPost('customer_id'));
                if (!$customer->getId()) {
                    throw new Exception($this->__('Not found customer to create transaction.'));
                }
                $transaction = Mage::helper('simirewardpoints/action')->addTransaction('admin',
                    $customer,
                    new Varien_Object(array(
                        'point_amount'  => $request->getPost('point_amount'),
                        'title'         => $request->getPost('title'),
                        'expiration_day'=> (int)$request->getPost('expiration_day'),
                    ))
                );
                if (!$transaction->getId()) {
                    throw new Exception(
                        $this->__('Cannot create transaction, please recheck form information.')
                    );
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Transaction has been created successfully.')
                );
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $transaction->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($request->getPost());
                $this->_redirect('*/*/edit', array('id' => $request->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('simirewardpoints')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }
    
    /**
     * complete reward points transaction
     */
    public function completeAction()
    {
        $transactionId  = $this->getRequest()->getParam('id');
        $transaction    = Mage::getModel('simirewardpoints/transaction')->load($transactionId);
        try {
            $transaction->completeTransaction();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__('Transaction has been completed successfully.')
            );
            $this->_redirect('*/*/edit', array('id' => $transaction->getId()));
            return ;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
    /**
     * cancel reward points transaction
     */
    public function cancelAction()
    {
        $transactionId  = $this->getRequest()->getParam('id');
        $transaction    = Mage::getModel('simirewardpoints/transaction')->load($transactionId);
        try {
            /*
            * xuanbinh
            */
           if($transaction->getAction() == 'receivepoint'){
               $tranferId = $transaction->getExtraContent();
               $arrExtra = explode("=",$tranferId);
               $transfer = Mage::getModel('simirewardpointstransfer/simirewardpointstransfer')->load($arrExtra[1]);
               $reason = Mage::helper('simirewardpointstransfer')->__('Transfer was canceled by admin.');
               Mage::helper('simirewardpointstransfer')->cancelTransfer($transfer,$reason);
           }else{
               $transaction->cancelTransaction();
           }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__('Transaction has been canceled successfully.')
            );
            $this->_redirect('*/*/edit', array('id' => $transaction->getId()));
            return ;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
    /**
     * expire reward points transaction
     */
    public function expireAction()
    {
        $transactionId  = $this->getRequest()->getParam('id');
        $transaction    = Mage::getModel('simirewardpoints/transaction')->load($transactionId);
        try {
            $transaction->expireTransaction();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__('Transaction has been expired successfully.')
            );
            $this->_redirect('*/*/edit', array('id' => $transaction->getId()));
            return ;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
    /**
     * mass complete transaction(s) action
     */
    public function massCompleteAction()
    {
        $tranIds = $this->getRequest()->getParam('transactions');
        if (!is_array($tranIds) || !count($tranIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            $collection = Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('point_amount', array('gt' => 0))
                ->addFieldToFilter('status', array(
                    'lt' => Simi_Simirewardpoints_Model_Transaction::STATUS_COMPLETED
                ))
                ->addFieldToFilter('transaction_id', array('in' => $tranIds));
            $total = 0;
            foreach ($collection as $transaction) {
                try {
                    $transaction->completeTransaction();
                    $total++;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            if ($total > 0) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d transaction(s) were successfully completed', $total)
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('No transaction was completed')
                );
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * mass cancel transaction(s) action
     */
    public function massCancelAction()
    {
        $tranIds = $this->getRequest()->getParam('transactions');
        if (!is_array($tranIds) || !count($tranIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            $collection = Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addFieldToFilter('point_amount', array('gt' => 0))
                ->addFieldToFilter('status', array(
                    'lteq' => Simi_Simirewardpoints_Model_Transaction::STATUS_COMPLETED
                ))
                ->addFieldToFilter('transaction_id', array('in' => $tranIds));
            $total = 0;
            foreach ($collection as $transaction) {
                try {
                    /*
                     * xuanbinh
                     */
                    if($transaction->getAction() == 'receivepoint'){
                        $tranferId = $transaction->getExtraContent();
                        $arrExtra = explode("=",$tranferId);
                        $transfer = Mage::getModel('simirewardpointstransfer/simirewardpointstransfer')->load($arrExtra[1]);
                        $reason = Mage::helper('simirewardpointstransfer')->__('Transfer was canceled by admin.');
                        Mage::helper('simirewardpointstransfer')->cancelTransfer($transfer,$reason);
                    }else{
                        $transaction->cancelTransaction();
                    }
                    /**
                     * end
                     */
                    $total++;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            if ($total > 0) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d transaction(s) were successfully canceled', $total)
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('No transaction was canceled')
                );
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * mass expire selected transaction(s)
     */
    public function massExpireAction()
    {
        $tranIds = $this->getRequest()->getParam('transactions');
        if (!is_array($tranIds) || !count($tranIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            $collection = Mage::getResourceModel('simirewardpoints/transaction_collection')
                ->addAvailableBalanceFilter()
                ->addFieldToFilter('status', array(
                    'lteq' => Simi_Simirewardpoints_Model_Transaction::STATUS_COMPLETED
                ))
                ->addFieldToFilter('expiration_date', array('notnull' => true))
                ->addFieldToFilter('expiration_date', array('to' => now()))
                ->addFieldToFilter('transaction_id', array('in' => $tranIds));
            
            $total = 0;
            foreach ($collection as $transaction) {
                try {
                    $transaction->expireTransaction();
                    $total++;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            if ($total > 0) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d transaction(s) were successfully expired', $total)
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('No transaction was expired')
                );
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'simirewardpoints_transaction.csv';
        $content    = $this->getLayout()
                           ->createBlock('simirewardpoints/adminhtml_transaction_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'simirewardpoints_transaction.xml';
        $content    = $this->getLayout()
                           ->createBlock('simirewardpoints/adminhtml_transaction_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simirewardpoints');
    }
}
