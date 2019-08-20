<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog manage products block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Vnecoms\Credit\Block\Adminhtml\Credit;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Dashboard extends \Magento\Backend\Block\Template
{
    /**
     * @var \Vnecoms\Credit\Model\CreditFactory
     */
    protected $creditFactory;
    
    /**
     * @var \Vnecoms\Credit\Model\Credit\TransactionFactory
     */
    protected $transactionFactory;
    
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    
    /**
     * @param \Vnecoms\Credit\Model\CreditFactory $creditFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Vnecoms\Credit\Model\CreditFactory $creditFactory,
        \Vnecoms\Credit\Model\Credit\TransactionFactory $transactionFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context,$data);
        $this->creditFactory = $creditFactory;
        $this->priceCurrency = $priceCurrency;
        $this->transactionFactory = $transactionFactory;
        $this->date = $date;
        return $this;
    }
    
    protected function _prepareLayout(){
        $this->addChild('transaction_grid', 'Vnecoms\Credit\Block\Adminhtml\Credit\Dashboard\Transaction\Grid');
        return parent::_prepareLayout();
    }
    /**
     * Get total credit in system
     * @return float
     */
    public function getTotalCreditInSystem(){
        if(!$this->getData('total_credit_in_system')){
            $this->setData('total_credit_in_system',$this->creditFactory->create()->getResource()->getTotalCreditInSystem());
        }
        return $this->getData('total_credit_in_system');
    }
    
    /**
     * Get total spent credit
     * @return float
     */
    public function getTotalSpentCredit(){
        return abs($this->transactionFactory->create()->getResource()->getTotalSpentCredit());
    }
    
    /**
     * Get Total Sold Credit
     * @return float
     */
    public function getTotalSoldCredit(){
        return $this->transactionFactory->create()->getResource()->getTotalSoldCredit();
    }
    
    /**
     * Get number of customer account with credit is greater than zero
     */
    public function getNumberCustomerWithCredit(){
        return $this->creditFactory->create()->getResource()->getNumberCustomerWithCredit();
    }
    /**
     * Get transaction data last 24 hours
     * 
     * @return string
     */
    public function getTransactionsDataLast24Hours(){
        $to = $this->date->date('Y-m-d H:i:s');
        $from = strtotime($to."-24hours");
        $from = $this->date->date('Y-m-d H:i:s',$from);
        
        $transactionResource = $this->transactionFactory->create()->getResource();
        $data = $transactionResource->getReceivedCreditTransactionsByHour($from,$to);
        $result = [];
        
        foreach($data as $trans){
            $result[$trans['time']] = [
                'y' => $trans['time'],
                'received' => $trans['credit'],
                'spent' => 0,
            ];
        }
        
        $data = $transactionResource->getSpentCreditTransactionsByHour($from,$to);
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
        $result = array_values($result);
        return json_encode($result);
    }
    
    /**
     * Get customer credit balance data
     * @return array
     */
    public function getCustomerCreditBalanceData(){
        if(!$this->getData('customer_credit_balance_data')){
            $creditCollection = $this->creditFactory->create()->getCollection();
            $creditCollection->join(array('customer_flat'=>$creditCollection->getTable('customer_grid_flat')), "customer_flat.entity_id=customer_id",['email'=>'email','name'=>'name'],null,'left');
            $creditCollection->setOrder('credit','desc');
            $creditCollection->setPageSize(6);
            $creditCollection->setCurPage(1)->load();
            $color = [
                ['color'=> '#f56954', 'highlight'=>'#f56954'],
                ['color'=> '#00a65a', 'highlight'=>'#00a65a'],
                ['color'=> '#f39c12', 'highlight'=>'#f39c12'],
                ['color'=> '#00c0ef', 'highlight'=>'#00c0ef'],
                ['color'=> '#3c8dbc', 'highlight'=>'#3c8dbc'],
                ['color'=> '#d2d6de', 'highlight'=>'#d2d6de'],
            ];
            $index = 0;
            $data = [];
            $totalCredit = $this->getTotalCreditInSystem();
            if($totalCredit > 0){
				$collectionSize = $creditCollection->count();
				$totalCreditOfTop5 = 0;
				$totalPercentOfTop5 = 0;
				foreach($creditCollection as $creditObj){
					$credit = $creditObj->getCredit();
					$percent = round(($credit/$totalCredit) * 100,0);
					if(($totalPercentOfTop5 + $percent) > 100) $percent = 100 - $totalPercentOfTop5;
					
					$data[] = [
						'value' => $credit,
						'color' => $color[$index]['color'],
						'highlight' => $color[$index]['highlight'],
						'label' => $creditObj->getName().' ('.$this->formatPrice($credit).')',
						'name' => $creditObj->getName(),
						'email' => $creditObj->getEmail(),
						'customer_id' => $creditObj->getCustomerId(),
						'percent' => ($index==$collectionSize-1)?100-$totalPercentOfTop5:$percent,
					];
					$totalCreditOfTop5 += $credit;
					$totalPercentOfTop5 += $percent;
					$index++;
					if($index > 4) break;
				}
				
				if($creditCollection->count() > 5){
					$credit = $totalCredit - $totalCreditOfTop5;
					$data[] = [
						'value' => $credit,
						'color' => $color[$index]['color'],
						'highlight' => $color[$index]['highlight'],
						'label' => __('Other customers (%1)',$this->formatPrice($credit)),
						'percent' => 100 - $totalPercentOfTop5
					];
				}
			}
            $this->setData('customer_credit_balance_data',$data);
        }
        return $this->getData('customer_credit_balance_data');
    }
    
    
    /**
     * Format Price currency
     * @param float $amount
     * @return string
     */
    public function formatPrice($amount){
        return $this->priceCurrency->format($amount,false);
    }
    
    /**
     * Get credit account URL
     * @return string
     */
    public function getCreditAccountUrl(){
        return $this->getUrl('storecredit/account');
    }
    
    /**
     * Get all transaction URL
     * @return string
     */
    public function getAllTransactionUrl(){
        return $this->getUrl('storecredit/transaction');
    }
    
}
