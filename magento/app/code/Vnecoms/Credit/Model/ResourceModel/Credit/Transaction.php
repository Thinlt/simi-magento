<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\Credit\Model\ResourceModel\Credit;

/**
 * Cms page mysql resource
 */
class Transaction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_store_credit_transaction', 'transaction_id');
    }

    /**
     * Get total credit in system.
     * @return float
     */
    public function getTotalSpentCredit(){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_store_credit_transaction'),
            ['total_credit' => 'SUM(amount)']
        )->where(
            'amount < 0'
        );
        
        $total = $connection->fetchOne($select);
        return $total;
    }
    
    /**
     * Get total credit in system.
     * @return float
     */
    public function getTotalSoldCredit(){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_store_credit_transaction'),
            ['total_credit' => 'SUM(amount)']
        )->where(
            'type = :type'
        );
        $bind = ['type' => \Vnecoms\Credit\Model\Processor\BuyCredit::TYPE];
        $total = $connection->fetchOne($select,$bind);
        return $total;
    }
    
    /**
     * Get total received credit by hour
     * @param string $from
     * @param string $to
     * @return multitype:
     */
    public function getReceivedCreditTransactionsByHour($from, $to='NOW()',$customerId=''){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_store_credit_transaction'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c-%d %H:00')",
                'credit' => "SUM(amount)"
            ]
        )->where(
            'created_at > :from'
        )->where(
            'created_at < :to'
        )->where(
            'amount > 0'
        );
        
        $bind = [
            'from' => $from,
            'to' => $to,
        ];
        
        if($customerId){
            $select->where('customer_id = :customer_id');
            $bind['customer_id'] = $customerId;
        }
        $select->group('time');
        
        $data = $connection->fetchAll($select,$bind);
        
        $newData = [];
        foreach($data as $trans){
            $newData[$trans['time']] = $trans;
        }

        $result = [];
        
        $datePointer = date("Y-n-d H:00",strtotime($from));
        $to = strtotime($to);
        while (strtotime($datePointer) < $to){            
            $result[$datePointer] = isset($newData[$datePointer])?$newData[$datePointer]:['time'=>$datePointer, 'credit'=>0];
            $datePointer = date("Y-n-d H:00",strtotime($datePointer."+1 hour"));
        }
        
        return $result;
    }
    
    /**
     * Get total spent credit by hour
     * @param string $from
     * @param string $to
     * @return multitype:
     */
    public function getSpentCreditTransactionsByHour($from, $to='NOW()',$customerId=''){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_store_credit_transaction'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c-%d %H:00')",
                'credit' => "SUM(amount)"
            ]
        )->where(
            'created_at > :from'
        )->where(
            'created_at < :to'
        )->where(
            'amount < 0'
        );
        
        $bind = [
            'from' => $from,
            'to' => $to,
        ];
        
        if($customerId){
            $select->where('customer_id = :customer_id');
            $bind['customer_id'] = $customerId;
        }
        $select->group('time');
        
        $data = $connection->fetchAll($select,$bind);

        return $data;
    }
    
    /**
     * Get total received credit by hour
     * @param string $from
     * @param string $to
     * @return multitype:
     */
    public function getReceivedCreditTransactionsByDay($from, $to='NOW()',$customerId=''){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_store_credit_transaction'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c-%d')",
                'credit' => "SUM(amount)"
            ]
        )->where(
            'created_at > :from'
        )->where(
            'created_at < :to'
        )->where(
            'amount > 0'
        );
        
        $bind = [
            'from' => $from,
            'to' => $to,
        ];
        
        if($customerId){
            $select->where('customer_id = :customer_id');
            $bind['customer_id'] = $customerId;
        }
        $select->group('time');
        
        $data = $connection->fetchAll($select,$bind);
        
        $newData = [];
        foreach($data as $trans){
            $newData[$trans['time']] = $trans;
        }
        
        $result = [];
        
        $datePointer = date("Y-n-d",strtotime($from));
        $to = strtotime($to);
        while (strtotime($datePointer) < $to){
            $result[$datePointer] = isset($newData[$datePointer])?$newData[$datePointer]:['time' => $datePointer, 'credit' => 0];
            $datePointer = date("Y-n-d",strtotime($datePointer."+1 day"));
        }
        
        return $result;
    }
    
    /**
    * Get total spent credit by hour
    * @param string $from
    * @param string $to
    * @return multitype:
    */
    public function getSpentCreditTransactionsByDay($from, $to='NOW()',$customerId=''){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_store_credit_transaction'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c-%d')",
                'credit' => "SUM(amount)"
            ]
        )->where(
            'created_at > :from'
        )->where(
            'created_at < :to'
        )->where(
            'amount < 0'
        );
        
        $bind = [
            'from' => $from,
            'to' => $to,
        ];
        
        if($customerId){
            $select->where('customer_id = :customer_id');
            $bind['customer_id'] = $customerId;
        }
        $select->group('time');
        
        $data = $connection->fetchAll($select,$bind);
    
        return $data;
    }
    
    /**
     * Get total received credit by hour
     * @param string $from
     * @param string $to
     * @return multitype:
     */
    public function getReceivedCreditTransactionsByMonth($from, $to='NOW()',$customerId=''){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_store_credit_transaction'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c')",
                'credit' => "SUM(amount)"
            ]
        )->where(
            'created_at > :from'
        )->where(
            'created_at < :to'
        )->where(
            'amount > 0'
        );
        
        $bind = [
            'from' => $from,
            'to' => $to,
        ];
        
        if($customerId){
            $select->where('customer_id = :customer_id');
            $bind['customer_id'] = $customerId;
        }
        $select->group('time');
        
        $data = $connection->fetchAll($select,$bind);
        
        $newData = [];
        foreach($data as $trans){
            $newData[$trans['time']] = $trans;
        }
        
        $result = [];
        
        $datePointer = date("Y-n",strtotime($from));
        $to = strtotime($to);
        while (strtotime($datePointer) < $to){
            $result[$datePointer] = isset($newData[$datePointer])?$newData[$datePointer]:['time' => $datePointer, 'credit' => 0];
            $datePointer = date("Y-n",strtotime($datePointer."+1 month"));
        }
        return $result;
    }
    
    /**
    * Get total spent credit by hour
    * @param string $from
    * @param string $to
    * @return multitype:
    */
    public function getSpentCreditTransactionsByMonth($from, $to='NOW()',$customerId=''){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_store_credit_transaction'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c')",
                'credit' => "SUM(amount)"
            ]
        )->where(
            'created_at > :from'
        )->where(
            'created_at < :to'
        )->where(
            'amount < 0'
        );
        
        $bind = [
            'from' => $from,
            'to' => $to,
        ];
        
        if($customerId){
            $select->where('customer_id = :customer_id');
            $bind['customer_id'] = $customerId;
        }
        $select->group('time');
        
        $data = $connection->fetchAll($select,$bind);
        
        return $data;
    }
}
