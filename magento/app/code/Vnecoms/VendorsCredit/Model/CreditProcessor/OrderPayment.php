<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Model\CreditProcessor;

use Magento\Framework\Exception\LocalizedException;

class OrderPayment extends \Vnecoms\Credit\Model\Processor\AbstractProcessor
{
    const TYPE = 'order_payment';
    
    protected $_action = 'add';
        
    /**
     * @see \Vnecoms\Credit\Model\Processor\AbstractProcessor::getTitle()
     */
    public function getTitle()
    {
        return __("Order Payment");
    }
    
    /**
     * @see \Vnecoms\Credit\Model\Processor\AbstractProcessor::getCode()
     */
    public function getCode()
    {
        return self::TYPE;
    }
    
    /**
     * Process data
     * @see \Vnecoms\Credit\Model\Processor\AbstractProcessor::process()
     */
    public function process($data = [])
    {
        if (!isset($data['amount'])) {
            throw new LocalizedException(__("Amout is not set in %1 on line %2", "<strong>".__FILE__."</strong>", "<strong>".__LINE__."</strong>"));
        }

        /*Process the credit amout*/
        $this->processAmount($data['amount']);
        
        $additionalInfo = 'vendor_invoice|'.$data['vendor_invoice']->getId();
        
        /*Create transasction*/
        $transData = [
            'customer_id'       => $this->getCreditAccount()->getCustomerId(),
            'type'              => self::TYPE,
            'amount'            => $data['amount'],
            'balance'           => $this->getCreditAccount()->getCredit(),
            'description'       => __("Credit from order #%1, invoice #%2", $data['order']->getIncrementId(), $data['vendor_invoice']->getInvoice()->getIncrementId()),
            'additional_info'   => $additionalInfo,
            'created_at'        => $this->date->timestamp(),
        ];
        $transaction = $this->transactionFactory->create();
        $transaction->setData($transData)->save();
        
        $this->sendNotificationEmail($transaction);
    }
}
