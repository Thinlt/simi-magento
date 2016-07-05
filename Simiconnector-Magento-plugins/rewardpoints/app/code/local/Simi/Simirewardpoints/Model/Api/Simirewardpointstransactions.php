<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 5/3/16
 * Time: 9:37 PM
 */
class Simi_Simirewardpoints_Model_Api_Simirewardpointstransactions extends Simi_Simiconnector_Model_Api_Abstract {

    protected $_DEFAULT_ORDER = 'transaction_id';

    public function setBuilderQuery() {
        $data = $this->getData();
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($data['resourceid']) {
                
            } else {
                $this->builderQuery = Mage::getModel('simirewardpoints/simiappmapping')->getHistory();
            }
        } else
            throw new Exception(Mage::helper('customer')->__('Please login First.'), 4);
    }

    public function index() {
        $result = parent::index();
        $actions = Mage::helper('simirewardpoints/action')->getActionsHash();
        $statuses = array(
            Simi_Simirewardpoints_Model_Transaction::STATUS_PENDING => 'pending',
            Simi_Simirewardpoints_Model_Transaction::STATUS_ON_HOLD => 'onhold',
            Simi_Simirewardpoints_Model_Transaction::STATUS_COMPLETED => 'completed',
            Simi_Simirewardpoints_Model_Transaction::STATUS_CANCELED => 'canceled',
            Simi_Simirewardpoints_Model_Transaction::STATUS_EXPIRED => 'expired'
        );
        $helper = Mage::helper('simirewardpoints/point');
        foreach ($result['simirewardpointstransactions'] as $index=>$transactionInfo) {
            $transaction = Mage::getModel('simirewardpoints/transaction')->load($transactionInfo['transaction_id']);
            $title = $transaction->getTitle();
            if ($title == '') {
                if (isset($actions[$transaction->getData('action')])) {
                    $title = $actions[$transaction->getData('action')];
                } else {
                    $title = $transaction->getData('action');
                }
            }
            $transactionInfo = array_merge($transactionInfo, array(
                'title' => $title,
                'point_amount' => (int) $transaction->getData('point_amount'),
                'point_label' => $helper->format($transaction->getData('point_amount')),
                'created_time' => $transaction->getData('created_time'),
                'expiration_date' => $transaction->getData('expiration_date') ? $transaction->getData('expiration_date') : '',
                'status' => $statuses[$transaction->getData('status')]
            ));
            $result['simirewardpointstransactions'][$index] = $transactionInfo;
        }
        return $result;
    }

}
