<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\Processor;

use Magento\Framework\Exception\LocalizedException;

abstract class AbstractProcessor implements \Vnecoms\Credit\Model\Processor\ProcessorInterface
{
    protected $_action = 'add';
    
    /**
     * @var \Vnecoms\Credit\Model\Credit
     */
    protected $_creditAccount;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    
    /**
     * @var \Vnecoms\Credit\Model\Credit\TransactionFactory
     */
    protected $transactionFactory;
    
    /**
     * @var \Vnecoms\Credit\Helper\Data
     */
    protected $_helper;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;
    
    /**
     * Get the title of the credit processor.
     * @return string
     */
    abstract public function getTitle();
    
    /**
     * Get processor code
     * @see \Vnecoms\Credit\Model\Processor\ProcessorInterface::getCode()
     */
    abstract public function getCode();
    
    public function __construct(
        \Vnecoms\Credit\Model\Credit\TransactionFactory $transactionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Vnecoms\Credit\Helper\Data $helper
    ) {
        $this->date = $date;
        $this->transactionFactory  = $transactionFactory;
        $this->_helper = $helper;
        $this->_localeDate = $localeDate;
    }
    
    /**
     * Set Credit Account
     * @param \Vnecoms\Credit\Model\Credit $creditAccount
     */
    public function setCreditAccount(\Vnecoms\Credit\Model\Credit $creditAccount){
        $this->_creditAccount = $creditAccount;
        return $this;
    }

    /**
     * Get credit account data.
     * @return \Vnecoms\Credit\Model\Credit
     */
    public function getCreditAccount(){
        return $this->_creditAccount;
    }
        
    /**
     * Process data
     * @see \Vnecoms\Credit\Model\Processor\ProcessorInterface::process()
     */
    public function process($data = array()){
        return $this;
    }
    
    /**
     * Process the amout
     * @param number $netAmount
     * @throws LocalizedException
     */
    public function processAmount($netAmount=0){
        if(!$this->_creditAccount || ! $this->_creditAccount instanceof \Vnecoms\Credit\Model\Credit)
            throw new LocalizedException(__('Credit Account is not set in %1 on line %2', "<strong>".__FILE__."</strong>","<strong>".__LINE__."</strong>"));
        
        switch ($this->_action){
            case 'add':
                $this->_creditAccount->addCredit($netAmount);
                break;
            case 'subtract':
                $this->_creditAccount->subtractCredit($netAmount);
                break;
        }
    }
    
    /**
     * Get credit transaction's description
     * @see \Vnecoms\Credit\Model\Processor\ProcessorInterface::getDescription()
     */
    public function getDescription(\Vnecoms\Credit\Model\Credit\Transaction $transaction){
        return $transaction->getDescription();
    }
    
    /**
     * Send credit balance change ntofication email
     * @see \Vnecoms\Credit\Model\Processor\ProcessorInterface::setNotificationEmail()
     * @return \Vnecoms\Credit\Model\Processor\AbstractProcessor
     */
    public function sendNotificationEmail(
        \Vnecoms\Credit\Model\Credit\Transaction $transaction
    ) {
        $transaction->setData('amount_formated',$this->_creditAccount->formatBaseCurrency($transaction->getAmount()))
            ->setData('balance_formated',$this->_creditAccount->formatBaseCurrency($transaction->getBalance()))
            ->setData('created_at_formated',$this->_localeDate->formatDate($this->_localeDate->date($transaction->getCreatedAt()),\IntlDateFormatter::FULL));
        $this->_helper->sendCreditBalanceChangeEmail($this->_creditAccount, $transaction);
        
        return $this;
    }
}
