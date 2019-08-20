<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsDashboard\Model;

/**
 * Adminhtml graph model
 *
 */
class Graph extends \Magento\Framework\DataObject
{
    /**
     * @var \Vnecoms\Credit\Model\Credit\TransactionFactory
     */
    protected $_transactionFactory;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    
    /**
     * @var \Vnecoms\VendorsSales\Model\OrderFactory
     */
    protected $_vendorOrderFactory;
    
    public function __construct(
        \Vnecoms\Credit\Model\Credit\TransactionFactory $transactionFactory,
        \Vnecoms\VendorsSales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_transactionFactory = $transactionFactory;
        $this->_vendorOrderFactory = $orderFactory;
        $this->_date = $date;
    }
    
    /**
     * Process received transactions data
     * @param array $data
     * @param array $result
     * @return array
     */
    protected function _processReceivedTransactionData($data = [], $result = [])
    {
        $key = '';
        foreach ($data as $trans) {
            if (!$key) {
                $key = $trans['time'];
            }
            $result[$trans['time']] = [
                'y' => $trans['time'],
                'received' => $trans['credit'],
                'spent' => 0,
            ];
        }
        return $result;
    }
    
    /**
     * Process received transactions data
     * @param array $data
     * @param array $result
     * @return array
     */
    protected function _processSpentTransactionData($data = [], $result = [])
    {
        foreach ($data as $trans) {
            if (isset($result[$trans['time']])) {
                $result[$trans['time']]['spent'] = abs($trans['credit']);
            } else {
                $result[$trans['time']] = [
                    'y' => $trans['time'],
                    'received' => 0,
                    'spent' => abs($trans['credit']),
                ];
            }
        }
        return $result;
    }
    
    /**
     * Get transaction data for graph last 24 hours
     *
     * @param int $customerId
     * @return array
     */
    public function getTransactionsDataLast24Hours($customerId)
    {
        $to = $this->_date->date('Y-m-d H:i:s');
        $from = strtotime($to."-24hours");
        $from = $this->_date->date('Y-m-d H:i:s', $from);
    
        $transactionResource = $this->_transactionFactory->create()->getResource();
        $result = [];
    
        $data = $transactionResource->getReceivedCreditTransactionsByHour($from, $to, $customerId);
        $result = $this->_processReceivedTransactionData($data, $result);
    
        $data = $transactionResource->getSpentCreditTransactionsByHour($from, $to, $customerId);
        $result = $this->_processSpentTransactionData($data, $result);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get transaction data for graph last 7 days
     *
     * @param int $customerId
     * @return array
     */
    public function getTransactionDataLast7Days($customerId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-7days");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
        $transactionResource = $this->_transactionFactory->create()->getResource();
        $result = [];
    
        $data = $transactionResource->getReceivedCreditTransactionsByDay($from, $to, $customerId);
        $result = $this->_processReceivedTransactionData($data, $result);
    
        $data = $transactionResource->getSpentCreditTransactionsByDay($from, $to, $customerId);
        $result = $this->_processSpentTransactionData($data, $result);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get transaction data for graph last month
     *
     * @param int $customerId
     * @return array
     */
    public function getTransactionDataLastMonth($customerId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-30days");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
        $transactionResource = $this->_transactionFactory->create()->getResource();
        $result = [];
    
        $data = $transactionResource->getReceivedCreditTransactionsByDay($from, $to, $customerId);
        $result = $this->_processReceivedTransactionData($data, $result);
    
        $data = $transactionResource->getSpentCreditTransactionsByDay($from, $to, $customerId);
        $result = $this->_processSpentTransactionData($data, $result);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get transaction data for graph last year
     *
     * @param int $customerId
     * @return array
     */
    public function getTransactionDataLastYear($customerId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-1year");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
        $transactionResource = $this->_transactionFactory->create()->getResource();
        $result = [];
    
        $data = $transactionResource->getReceivedCreditTransactionsByMonth($from, $to, $customerId);
        $result = $this->_processReceivedTransactionData($data, $result);
    
        $data = $transactionResource->getSpentCreditTransactionsByMonth($from, $to, $customerId);
        $result = $this->_processSpentTransactionData($data, $result);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get transaction data for graph last 2 years
     *
     * @param int $customerId
     * @return array
     */
    public function getTransactionDataLast2Years($customerId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-2year");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
        $transactionResource = $this->_transactionFactory->create()->getResource();
        $result = [];
    
        $data = $transactionResource->getReceivedCreditTransactionsByMonth($from, $to, $customerId);
        $result = $this->_processReceivedTransactionData($data, $result);
    
        $data = $transactionResource->getSpentCreditTransactionsByMonth($from, $to, $customerId);
        $result = $this->_processSpentTransactionData($data, $result);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Process received transactions data
     * @param array $data
     * @param array $result
     * @return array
     */
    protected function _processOrderData($data = [])
    {
        $key = '';
        foreach ($data as $trans) {
            if (!$key) {
                $key = $trans['time'];
            }
            $result[$trans['time']] = [
                'y' => $trans['time'],
                'order_num' => isset($trans['order_num'])?$trans['order_num']:0,
                'amount' => isset($trans['amount'])?$trans['amount']:0,
            ];
        }
        return $result;
    }
    
    /**
     * Get order data last 24 hours
     *
     * @param int $vendorId
     * @return multitype:
     */
    public function getOrdersDataLast24Hours($vendorId)
    {
        $to = $this->_date->date('Y-m-d H:i:s');
        $from = strtotime($to."-24hours");
        $from = $this->_date->date('Y-m-d H:i:s', $from);
        
        $orderResource = $this->_vendorOrderFactory->create()->getResource();
        $data = $orderResource->getNumOfOrderByHour($from, $to, $vendorId);
        $result = $this->_processOrderData($data);
        
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get order data last 7 days
     *
     * @param int $vendorId
     * @return multitype:
     */
    public function getOrdersDataLast7Days($vendorId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-7days");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
        $orderResource = $this->_vendorOrderFactory->create()->getResource();
        $data = $orderResource->getNumOfOrderByDay($from, $to, $vendorId);
        $result = $this->_processOrderData($data);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get order data for graph last month
     *
     * @param int $customerId
     * @return array
     */
    public function getOrdersDataLastMonth($vendorId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-30days");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
        
        $orderResource = $this->_vendorOrderFactory->create()->getResource();
        $data = $orderResource->getNumOfOrderByDay($from, $to, $vendorId);
        $result = $this->_processOrderData($data);
    
        $result = array_values($result);
        return $result;
    }
    
    
    /**
     * Get orders data for graph last year
     *
     * @param int $customerId
     * @return array
     */
    public function getOrdersDataLastYear($vendorId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-1year");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
        
        $orderResource = $this->_vendorOrderFactory->create()->getResource();
        $data = $orderResource->getNumOfOrderByMonth($from, $to, $vendorId);
        $result = $this->_processOrderData($data);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get orders data for graph last 2 years
     *
     * @param int $customerId
     * @return array
     */
    public function getOrdersDataLast2Years($vendorId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-2year");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
        
        $orderResource = $this->_vendorOrderFactory->create()->getResource();
        $data = $orderResource->getNumOfOrderByMonth($from, $to, $vendorId);
        $result = $this->_processOrderData($data);
    
        $result = array_values($result);
        return $result;
    }
    
    
    /**
     * Get paid amount last 24 hours
     *
     * @param int $vendorId
     * @return multitype:
     */
    public function getAmountsDataLast24Hours($vendorId)
    {
        $to = $this->_date->date('Y-m-d H:i:s');
        $from = strtotime($to."-24hours");
        $from = $this->_date->date('Y-m-d H:i:s', $from);
    
        $orderResource = $this->_vendorOrderFactory->create()->getResource();
        $data = $orderResource->getAmountsByHour($from, $to, $vendorId);
        $result = $this->_processOrderData($data);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get paid amount last 7 days
     *
     * @param int $vendorId
     * @return multitype:
     */
    public function getAmountsDataLast7Days($vendorId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-7days");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
        $orderResource = $this->_vendorOrderFactory->create()->getResource();
        $data = $orderResource->getAmountsByDay($from, $to, $vendorId);
        $result = $this->_processOrderData($data);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get paid amount last month
     *
     * @param int $customerId
     * @return array
     */
    public function getAmountsDataLastMonth($vendorId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-30days");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
    
        $orderResource = $this->_vendorOrderFactory->create()->getResource();
        $data = $orderResource->getAmountsByDay($from, $to, $vendorId);
        $result = $this->_processOrderData($data);
    
        $result = array_values($result);
        return $result;
    }
    
    
    /**
     * Get paid amount last year
     *
     * @param int $customerId
     * @return array
     */
    public function getAmountsDataLastYear($vendorId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-1year");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
    
        $orderResource = $this->_vendorOrderFactory->create()->getResource();
        $data = $orderResource->getAmountsByMonth($from, $to, $vendorId);
        $result = $this->_processOrderData($data);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get paid amount last 2 years
     *
     * @param int $customerId
     * @return array
     */
    public function getAmountsDataLast2Years($vendorId)
    {
        $to = $this->_date->date('Y-m-d');
        $from = strtotime($to."-2year");
        $from = $this->_date->date('Y-m-d', $from);
        $to .= ' 23:59:59';
    
        $orderResource = $this->_vendorOrderFactory->create()->getResource();
        $data = $orderResource->getAmountsByMonth($from, $to, $vendorId);
        $result = $this->_processOrderData($data);
    
        $result = array_values($result);
        return $result;
    }
}
