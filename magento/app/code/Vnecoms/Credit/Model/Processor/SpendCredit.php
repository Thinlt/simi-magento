<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\Processor;

use Magento\Framework\Exception\LocalizedException;

class SpendCredit extends \Vnecoms\Credit\Model\Processor\AbstractProcessor
{
    const TYPE = 'spend_credit';
    
    protected $_action = 'subtract';
    
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;
    
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    
    public function __construct(
        \Vnecoms\Credit\Model\Credit\TransactionFactory $transactionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Vnecoms\Credit\Helper\Data $helper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->_orderFactory = $orderFactory;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($transactionFactory, $date, $localeDate, $helper);
    }
    
    /**
     * @see \Vnecoms\Credit\Model\Processor\AbstractProcessor::getTitle()
     */
    public function getTitle(){
        return __("Spend Credit");
    }
    
    /**
     * @see \Vnecoms\Credit\Model\Processor\AbstractProcessor::getCode()
     */
    public function getCode(){
        return self::TYPE;
    }
    
    /**
     * Process data
     * @see \Vnecoms\Credit\Model\Processor\AbstractProcessor::process()
     */
    public function process($data=array()){
        if(!isset($data['amount'])) 
            throw new LocalizedException(
                __("Amout is not set in %1 on line %2", "<strong>".
                __FILE__."</strong>","<strong>".__LINE__."</strong>")
            );

        $amount = abs($data['amount']);
        /*Process the credit amout*/
        $this->processAmount($amount);
        
        $additionalInfo = 'order|'.$data['order']->getId();
        
        /*Create transasction*/
        $transData = [
            'customer_id'		=> $this->getCreditAccount()->getCustomerId(),
            'type'				=> self::TYPE,
            'amount'			=> -$amount,
            'balance'			=> $this->getCreditAccount()->getCredit(),
            'description'		=> __("Spent credit on order #%1",$data['order']->getIncrementId()),
            'additional_info'	=> $additionalInfo,
            'created_at'		=> $this->date->timestamp(),
        ];
        $transaction = $this->transactionFactory->create();
        $transaction->setData($transData)->save();
        
        $this->sendNotificationEmail($transaction);
    }
    
    /**
     * Get Transaction Description
     * @see \Vnecoms\Credit\Model\Processor\AbstractProcessor::getDescription()
     */
    public function getDescription(\Vnecoms\Credit\Model\Credit\Transaction $transaction){
        $orderData = $transaction->getAdditionalInfo();
        $orderData = explode("|", $orderData);
        if(sizeof($orderData) < 2) return parent::getDescription($transaction);
    
        $order = $this->_orderFactory->create()->load($orderData[1]);
        $orderLink = '<a href="'.$this->urlBuilder->getUrl('sales/order/view',array('order_id'=>$order->getId())).'">'.$order->getIncrementId().'</a>';
        return __("Spent credit on order #%1",$orderLink);
    }
}
