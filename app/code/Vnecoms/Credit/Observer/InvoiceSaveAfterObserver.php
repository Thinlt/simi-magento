<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\Credit\Model\Product\Type\Credit as CreditType;
use Vnecoms\Credit\Model\Processor\BuyCredit as BuyCreditProcessor;
use Vnecoms\Credit\Model\Processor\SpendCredit as SpendCreditProcessor;

class InvoiceSaveAfterObserver implements ObserverInterface
{
    /**
     * @var \Vnecoms\Credit\Model\Processor
     */
    protected $creditProcessor;
    
    /**
     * @var \Vnecoms\Credit\Model\CreditFactory
     */
    protected $creditAccountFactory;
    
    /**
     * @var \Vnecoms\Credit\Model\Credit\TransactionFactory
     */
    protected $transactionFactory;
    
    public function __construct(
        \Vnecoms\Credit\Model\Processor $creditProcessor,
        \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory,
        \Vnecoms\Credit\Model\Credit\TransactionFactory $transactionFactory
    ) {
        $this->transactionFactory   = $transactionFactory;
        $this->creditAccountFactory = $creditAccountFactory;
        $this->creditProcessor      = $creditProcessor;
    }
    
    /**
     * Add the notification if there are any vendor awaiting for approval. 
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice	= $observer->getInvoice();
    	
    	/*Return if the invoice is not paid*/
    	if($invoice->getState() != \Magento\Sales\Model\Order\Invoice::STATE_PAID) return;
    	
    	/*Buy credit*/
    	$this->processBuyCredit($invoice);
    }
    
    /**
     * Process buy credit transaction
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     */
    public function processBuyCredit(\Magento\Sales\Model\Order\Invoice $invoice){
        $order = $invoice->getOrder();
        
        $customerId = $order->getCustomerId();
        if(!$customerId) return;
         
        $creditAccount = $this->creditAccountFactory->create();
        $creditAccount->loadByCustomerId($customerId);
        
        /*Return if the transaction for the invoice is already exist.*/
        $trans = $this->transactionFactory->create()->getCollection()
        ->addFieldToFilter('type',BuyCreditProcessor::TYPE)
        ->addFieldToFilter('additional_info',array('like'=>'invoice|'.$invoice->getId()));
        if($trans->count()) return;
         
         
        /* Add money to credit account*/
        foreach($invoice->getAllItems() as $item){
            $orderItem 	= $item->getOrderItem();
            if($orderItem->getParentItemId()) continue;
            	
            if($orderItem->getProductType() != CreditType::TYPE_CODE) continue;
            $creditOption = $orderItem->getProductOptions();
            $creditValue = isset($creditOption['store_credit']['credit_value'])?$creditOption['store_credit']['credit_value']:0;
            if($creditValue > 0){
                $creditValue   = round($creditValue,2);
                $creditAmount  = $creditValue * $item->getQty();
                 
                $data = array(
                    'type'		=> BuyCreditProcessor::TYPE,
                    'amount'	=> $creditAmount,
                    'order'		=> $order,
                    'invoice'	=> $invoice,
                    'invoice_item' => $item,
                );
                 
                $this->creditProcessor->process($creditAccount,$data);
            }
        }
    }
}
