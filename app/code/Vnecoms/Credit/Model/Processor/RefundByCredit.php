<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\Processor;

use Magento\Framework\Exception\LocalizedException;

class RefundByCredit extends \Vnecoms\Credit\Model\Processor\AbstractProcessor
{
    const TYPE = 'refund_by_credit';
    
    protected $_action = 'add';
    
    /**
     * @var \Magento\Sales\Model\Order\CreditmemoFactory
     */
    protected $creditmemo;
    
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
        \Magento\Sales\Model\Order\Creditmemo $creditmemo,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->creditmemo = $creditmemo;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($transactionFactory, $date, $localeDate, $helper);
    }
    
    /**
     * @see \Vnecoms\Credit\Model\Processor\AbstractProcessor::getTitle()
     */
    public function getTitle(){
        return __("Order Refunded by Credit");
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
            throw new LocalizedException(__("Amout is not set in %1 on line %2", "<strong>".__FILE__."</strong>","<strong>".__LINE__."</strong>"));

        /*Process the credit amout*/
        $this->processAmount($data['amount']);
        
        $additionalInfo = 'creditmemo|'.$data['creditmemo']->getId();
        
        /*Create transasction*/
        $transData = [
            'customer_id'		=> $this->getCreditAccount()->getCustomerId(),
            'type'				=> self::TYPE,
            'amount'			=> $data['amount'],
            'balance'			=> $this->getCreditAccount()->getCredit(),
            'description'		=> __("Order refunded #%1, Creditmemo #%2",$data['order']->getIncrementId(), $data['creditmemo']->getIncrementId()),
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
        $creditmemoData = $transaction->getAdditionalInfo();
        $creditmemoData = explode("|", $creditmemoData);
        if(sizeof($creditmemoData) < 2) return parent::getDescription($transaction);
        
        $creditmemo = $this->creditmemo->load($creditmemoData[1]);
        $order = $creditmemo->getOrder();
        $orderLink = '<a href="'.$this->urlBuilder->getUrl('sales/order/view',array('order_id'=>$order->getId())).'">'.$order->getIncrementId().'</a>';
        return __("Order refunded #%1, Creditmemo #%2",$orderLink, $creditmemo->getIncrementId());
    }
}
