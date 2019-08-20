<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Controller\Adminhtml\Dashboard;

use Vnecoms\Credit\Controller\Adminhtml\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

class Graph extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    
    /**
     * @var \Vnecoms\Credit\Model\Credit\TransactionFactory
     */
    protected $transactionFactory;
    
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Date $dateFilter,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Vnecoms\Credit\Model\Credit\TransactionFactory $transactionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context,$coreRegistry, $dateFilter);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->date = $date;
        $this->transactionFactory = $transactionFactory;
    }
    
    /**
     * @return void
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $period = $this->getRequest()->getParam('period');
        switch($period){
            case '2y':
                $response->setData($this->getTransactionDataLast2Years());
                break;
            case '1y':
                $response->setData($this->getTransactionDataLastYear());
                break;
            case '1m':
                $response->setData($this->getTransactionDataLastMonth());
                break;
            case '7d':
                $response->setData($this->getTransactionDataLast7Days());
                break;
            case '24h':
            default:
                $response->setData($this->getTransactionsDataLast24Hours());

        }
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
    
    /**
     * Process received transactions data
     * @param array $data
     * @param array $result
     */
    protected function _processReceivedData($data=[], $result=[]){
        foreach($data as $trans){
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
     */
    protected function _processSpentData($data=[], $result=[]){
        foreach($data as $trans){
            if(!isset($result[$trans['time']])){
                $result[$trans['time']] = [
                    'y' => $trans['time'],
                    'received' => 0,
                    'spent' => abs($trans['credit']),
                ];
                continue;
            }
            
            $result[$trans['time']]['spent'] = abs($trans['credit']);
        }
        return $result;
    }
    
    /**
     * Get transaction data for graph last 24 hours
     * @return multitype:
     */
    public function getTransactionsDataLast24Hours(){
        $to = $this->date->date('Y-m-d H:i:s');
        $from = strtotime($to."-24hours");
        $from = $this->date->date('Y-m-d H:i:s',$from);
    
        $transactionResource = $this->transactionFactory->create()->getResource();
        $result = [];
        
        $data = $transactionResource->getReceivedCreditTransactionsByHour($from,$to);
        $result = $this->_processReceivedData($data,$result);
        
        $data = $transactionResource->getSpentCreditTransactionsByHour($from,$to);
        $result = $this->_processSpentData($data,$result);
        
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get transaction data for graph last 7 days
     * @return multitype:
     */
    public function getTransactionDataLast7Days(){
        $to = $this->date->date('Y-m-d');
        $from = strtotime($to."-7days");
        $from = $this->date->date('Y-m-d',$from);
        $to .= ' 23:59:59';
        $transactionResource = $this->transactionFactory->create()->getResource();
        $result = [];
        
        $data = $transactionResource->getReceivedCreditTransactionsByDay($from,$to);        
        $result = $this->_processReceivedData($data,$result);

        $data = $transactionResource->getSpentCreditTransactionsByDay($from,$to);
        $result = $this->_processSpentData($data,$result);
        
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get transaction data for graph last 7 days
     * @return multitype:
     */
    public function getTransactionDataLastMonth(){
        $to = $this->date->date('Y-m-d');
        $from = strtotime($to."-30days");
        $from = $this->date->date('Y-m-d',$from);
        $to .= ' 23:59:59';
        $transactionResource = $this->transactionFactory->create()->getResource();
        $result = [];
    
        $data = $transactionResource->getReceivedCreditTransactionsByDay($from,$to);
        $result = $this->_processReceivedData($data,$result);
    
        $data = $transactionResource->getSpentCreditTransactionsByDay($from,$to);
        $result = $this->_processSpentData($data,$result);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get transaction data for graph last 7 days
     * @return multitype:
     */
    public function getTransactionDataLastYear(){
        $to = $this->date->date('Y-m-d');
        $from = strtotime($to."-1year");
        $from = $this->date->date('Y-m-d',$from);
        $to .= ' 23:59:59';
        $transactionResource = $this->transactionFactory->create()->getResource();
        $result = [];
    
        $data = $transactionResource->getReceivedCreditTransactionsByMonth($from,$to);
        $result = $this->_processReceivedData($data,$result);
    
        $data = $transactionResource->getSpentCreditTransactionsByMonth($from,$to);
        $result = $this->_processSpentData($data,$result);
    
        $result = array_values($result);
        return $result;
    }
    
    /**
     * Get transaction data for graph last 7 days
     * @return multitype:
     */
    public function getTransactionDataLast2Years(){
        $to = $this->date->date('Y-m-d');
        $from = strtotime($to."-2year");
        $from = $this->date->date('Y-m-d',$from);
        $to .= ' 23:59:59';
        $transactionResource = $this->transactionFactory->create()->getResource();
        $result = [];
    
        $data = $transactionResource->getReceivedCreditTransactionsByMonth($from,$to);
        $result = $this->_processReceivedData($data,$result);
    
        $data = $transactionResource->getSpentCreditTransactionsByMonth($from,$to);
        $result = $this->_processSpentData($data,$result);
    
        $result = array_values($result);
        return $result;
    }
}
