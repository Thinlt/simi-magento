<?php
/**
 * Copyright © Vnecoms. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsSales\Model\ResourceModel;

/**
 * Cms page mysql resource
 */
class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_vendor_sales_order', 'entity_id');
    }
    
    /**
     * Update vendor order id for order items.
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterSave($object);
        if($object->getItems() && is_array($object->getItems()) && sizeof($object->getItems())){
            $itemIds = [];
            foreach($object->getItems() as $orderItem){
                $itemIds[] = $orderItem->getId();
            }
            
            $adapter   = $this->getConnection();
            $sql = "UPDATE ".$this->getTable('sales_order_item').' SET vendor_order_id="'.$object->getId().'" WHERE item_id IN('.implode(",", $itemIds).')';
            return $adapter->query($sql);
        }
        
        
        return $this;
    }
    
    /**
     * Get Vendor Order Id by order id and vendor id
     * 
     * @param int $orderId
     * @param int $vendorId
     */
    public function getVendorOrderId($vendorId, $orderId){
        $table = $this->getTable('ves_vendor_sales_order');
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_sales_order'),
            ['entity_id']
        )->where(
            'order_id = :order_id'
        )->where(
            'vendor_id = :vendor_id'
        );
        $bind = ['vendor_id' => $vendorId, 'order_id' => $orderId];

        $vendorOrderId = $connection->fetchOne($select, $bind);
        return $vendorOrderId;
    }
    
    /**
     * Get lifetime sales (base amount)
     * 
     * @param int $vendorId
     * @return float
     */
    public function getLifetimeSales($vendorId){
        $table = $this->getTable('ves_vendor_sales_order');
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_sales_order'),
            ['lifetime_sales' => 'SUM(base_total_paid)']
        )->where(
            'vendor_id = :vendor_id'
        );
        $bind = ['vendor_id' => $vendorId];
        
        $total = $connection->fetchOne($select, $bind);

        return $total;
    }
    
    /**
     * Get average orders
     * 
     * @param int $vendorId
     * @return float:
     */
    public function getAverageOrders($vendorId){
        $table = $this->getTable('ves_vendor_sales_order');
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_sales_order'),
            [
                'lifetime_sales' => 'SUM(base_total_paid)',
                'sales_num' => 'count( entity_id )'
            ]
        )->where(
            'vendor_id = :vendor_id'
        );
        $bind = ['vendor_id' => $vendorId];
        
        $total = $connection->fetchRow($select,$bind);
        
        if(!isset($total['lifetime_sales']) || !isset($total['sales_num']) || !($total['lifetime_sales']) || !($total['sales_num']))
            return 0;
        return $total['lifetime_sales']/$total['sales_num'];
    }
    
    /**
     * Get sales count
     * 
     * @param int $vendorId
     * @return number
     */
    public function getSalesCount($vendorId){
        $table = $this->getTable('ves_vendor_sales_order');
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_sales_order'),
            [
                'sales_num' => 'count( entity_id )'
            ]
        )->where(
            'vendor_id = :vendor_id'
        )->where(
            'base_total_paid > 0'
        );;
        $bind = ['vendor_id' => $vendorId];
        
        $count = $connection->fetchOne($select,$bind);
        
        return $count;
    }
    
    /**
     * Get total received credit by hour
     * 
     * @param string $from
     * @param string $to
     * @param int $vendorId
     * @return array|null
     */
    public function getNumOfOrderByHour($from, $to='NOW()',$vendorId){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_sales_order'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c-%d %H:00')",
                'order_num' => 'count( entity_id )'
            ]
        )->where(
            'created_at > :from_date'
        )->where(
            'created_at < :to_date'
        )->where(
            'vendor_id = :vendor_id'
        )->group('time');

        $bind = [
            'from_date' => $from,
            'to_date' => $to,
            'vendor_id' => $vendorId
        ];
        $data = $connection->fetchAll($select,$bind);
        
        $newData = [];
        foreach($data as $trans){
            $newData[$trans['time']] = $trans;
        }
    
        $result = [];
    
        $datePointer = date("Y-n-d H:00",strtotime($from));
        $to = strtotime($to);
        while (strtotime($datePointer) < $to){
            if(!isset($newData[$datePointer])){
                $result[$datePointer] =['time'=>$datePointer, 'order_num'=>0];
            }else{
                $result[$datePointer] = $newData[$datePointer];
            }
        
            $datePointer = date("Y-n-d H:00",strtotime($datePointer."+1 hour"));
        }
        return $result;
    }
    
    
    /**
     * Get total received credit by day
     * 
     * @param string $from
     * @param string $to
     * @param int $vendorId
     * @return array|null
     */
    public function getNumOfOrderByDay($from, $to='NOW()',$vendorId=false){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_sales_order'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c-%d')",
                'order_num' => 'count( entity_id )'
            ]
        )->where(
            'created_at > :from_date'
        )->where(
            'created_at < :to_date'
        )->where(
            'vendor_id = :vendor_id'
        )->group('time');
        
        $bind = [
            'from_date' => $from,
            'to_date' => $to,
            'vendor_id' => $vendorId
        ];
        $data = $connection->fetchAll($select,$bind);
        
        $newData = [];
        foreach($data as $trans){
            $newData[$trans['time']] = $trans;
        }
    
        $result = [];
    
        $datePointer = date("Y-n-d",strtotime($from));
        $to = strtotime($to);
        while (strtotime($datePointer) < $to){
            if(!isset($newData[$datePointer])){
                $result[$datePointer] =['time'=>$datePointer, 'order_num'=>0];
            }else{
                $result[$datePointer] = $newData[$datePointer];
            }
    
            $datePointer = date("Y-n-d",strtotime($datePointer."+1 day"));
        }
    
        return $result;
    }
    
    
    /**
     * Get total received credit by month
     * 
     * @param string $from
     * @param string $to
     * @param int $vendorId
     * @return array|null
     */
    public function getNumOfOrderByMonth($from, $to='NOW()',$vendorId=false){
       $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_sales_order'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c')",
                'order_num' => 'count( entity_id )'
            ]
        )->where(
            'created_at > :from_date'
        )->where(
            'created_at < :to_date'
        )->where(
            'vendor_id = :vendor_id'
        )->group('time');
        
        $bind = [
            'from_date' => $from,
            'to_date' => $to,
            'vendor_id' => $vendorId
        ];
        $data = $connection->fetchAll($select,$bind);
        
        $newData = [];
        foreach($data as $trans){
            $newData[$trans['time']] = $trans;
        }
    
        $result = [];
    
        $datePointer = date("Y-n",strtotime($from));
        $to = strtotime($to);
        while (strtotime($datePointer) < $to){
            if(!isset($newData[$datePointer])){
                $result[$datePointer] =['time'=>$datePointer, 'order_num'=>0];
            }else{
                $result[$datePointer] = $newData[$datePointer];
            }
    
            $datePointer = date("Y-n",strtotime($datePointer."+1 month"));
        }
        return $result;
    }
    
    /**
     * Get total money received by hour
     *
     * @param string $from
     * @param string $to
     * @param int $vendorId
     * @return array|null
     */
    public function getAmountsByHour($from, $to='NOW()',$vendorId){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_sales_order'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c-%d %H:00')",
                'amount' => 'sum( base_total_paid )'
            ]
        )->where(
            'created_at > :from_date'
        )->where(
            'created_at < :to_date'
        )->where(
            'vendor_id = :vendor_id'
        )->group('time');
    
        $bind = [
            'from_date' => $from,
            'to_date' => $to,
            'vendor_id' => $vendorId
        ];
        $data = $connection->fetchAll($select,$bind);
    
        $newData = [];
        foreach($data as $trans){
            $newData[$trans['time']] = $trans;
        }
    
        $result = [];
    
        $datePointer = date("Y-n-d H:00",strtotime($from));
        $to = strtotime($to);
        while (strtotime($datePointer) < $to){
            if(!isset($newData[$datePointer])){
                $result[$datePointer] =['time'=>$datePointer, 'amount'=>0];
            }else{
                $result[$datePointer] = $newData[$datePointer];
            }
    
            $datePointer = date("Y-n-d H:00",strtotime($datePointer."+1 hour"));
        }
        return $result;
    }
    
    /**
     * Get total money by day
     *
     * @param string $from
     * @param string $to
     * @param int $vendorId
     * @return array|null
     */
    public function getAmountsByDay($from, $to='NOW()',$vendorId=false){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_sales_order'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c-%d')",
                'amount' => 'sum( base_total_paid )'
            ]
        )->where(
            'created_at > :from_date'
        )->where(
            'created_at < :to_date'
        )->where(
            'vendor_id = :vendor_id'
        )->group('time');
    
        $bind = [
            'from_date' => $from,
            'to_date' => $to,
            'vendor_id' => $vendorId
        ];
        $data = $connection->fetchAll($select,$bind);
    
        $newData = [];
        foreach($data as $trans){
            $newData[$trans['time']] = $trans;
        }
    
        $result = [];
    
        $datePointer = date("Y-n-d",strtotime($from));
        $to = strtotime($to);
        while (strtotime($datePointer) < $to){
            if(!isset($newData[$datePointer])){
                $result[$datePointer] =['time'=>$datePointer, 'amount'=>0];
            }else{
                $result[$datePointer] = $newData[$datePointer];
            }
    
            $datePointer = date("Y-n-d",strtotime($datePointer."+1 day"));
        }
    
        return $result;
    }
    
    /**
     * Get total received credit by month
     *
     * @param string $from
     * @param string $to
     * @param int $vendorId
     * @return array|null
     */
    public function getAmountsByMonth($from, $to='NOW()',$vendorId=false){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_sales_order'),
            [
                'time' => "DATE_FORMAT(created_at,'%Y-%c')",
                'amount' => 'sum( base_total_paid)'
            ]
        )->where(
            'created_at > :from_date'
        )->where(
            'created_at < :to_date'
        )->where(
            'vendor_id = :vendor_id'
        )->group('time');
    
        $bind = [
            'from_date' => $from,
            'to_date' => $to,
            'vendor_id' => $vendorId
        ];
        $data = $connection->fetchAll($select,$bind);
    
        $newData = [];
        foreach($data as $trans){
            $newData[$trans['time']] = $trans;
        }
    
        $result = [];
    
        $datePointer = date("Y-n",strtotime($from));
        $to = strtotime($to);
        while (strtotime($datePointer) < $to){
            if(!isset($newData[$datePointer])){
                $result[$datePointer] =['time'=>$datePointer, 'amount'=>0];
            }else{
                $result[$datePointer] = $newData[$datePointer];
            }
    
            $datePointer = date("Y-n",strtotime($datePointer."+1 month"));
        }
        return $result;
    }

    public function isCreatedVendorOrder($invoiceId){
        $table = $this->getTable('ves_vendor_sales_order');
        $readCollection = $this->getConnection();
        $sql = "SELECT count(entity_id) as invoice_num FROM $table WHERE order_id=\"{$invoiceId}\";";
        $count = $readCollection->fetchOne($sql);
        return $count > 0;
    }
}
