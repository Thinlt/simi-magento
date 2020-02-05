<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model\Processor;

use \Vnecoms\Credit\Model\Credit\Transaction;

interface ProcessorInterface 
{
    public function process($data = array());
    
    public function processAmount($amount);
    
    public function getTitle();
    
    public function getCode();
    
    public function getDescription(Transaction $transaction);
    
    public function sendNotificationEmail(Transaction $transaction);
}
