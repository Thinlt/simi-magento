<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\Processor;

use Magento\Framework\Exception\LocalizedException;

class AdminSubtractCredit extends \Vnecoms\Credit\Model\Processor\AbstractProcessor
{
    const TYPE = 'admin_subtract_credit';
    
    protected $_action = 'subtract';
        
    /**
     * @see \Vnecoms\Credit\Model\Processor\AbstractProcessor::getTitle()
     */
    public function getTitle(){
        return __("Admin Subtract Credit");
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

        $data['amount'] = abs($data['amount']);
        
        /*Process the credit amout*/
        $this->processAmount($data['amount']);
        
        $additionalInfo = '';
        $description = isset($data['description'])&& $data['description']?$data['description']:__("Admin subtract %1 credits from your credit account.",$this->_creditAccount->formatBaseCurrency($data['amount']));
        /*Create transasction*/
        $transData = [
            'customer_id'		=> $this->getCreditAccount()->getCustomerId(),
            'type'				=> self::TYPE,
            'amount'			=> -$data['amount'],
            'balance'			=> $this->getCreditAccount()->getCredit(),
            'description'		=> $description,
            'additional_info'	=> $additionalInfo,
            'created_at'		=> $this->date->timestamp(),
        ];
        $transaction = $this->transactionFactory->create();
        $transaction->setData($transData)->save();
        
        $this->sendNotificationEmail($transaction);
    }
}
