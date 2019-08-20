<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\Credit\Model\Processor\RefundByCredit;

/**
 * AdminNotification observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class CreditmemoSaveAfter implements ObserverInterface
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
     * Before register credit memo, add credit to customer credit account.
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getCreditmemo();

        /*Process this only for registered customer*/
        if(!$creditmemo->getOrder()->getCustomerId()) return;
        
        /*Return if the transaction is made already*/
        $transactionCollection = $this->transactionFactory->create()->getCollection();
        $transactionCollection->addFieldToFilter('type',\Vnecoms\Credit\Model\Processor\RefundByCredit::TYPE)
            ->addFieldToFilter('additional_info',array('like'=>'creditmemo|'.$creditmemo->getId()));
        if($transactionCollection->count()) return;
        
        if(!$creditmemo->getBaseCreditRefunded()) return;

        /*Init Credit Account*/
        $creditAccount = $this->creditAccountFactory->create();
        $creditAccount->loadByCustomerId($creditmemo->getOrder()->getCustomerId());
        
        /*Add credit to customer credit amount*/
        $data = array(
            'type'		=> RefundByCredit::TYPE,
            'amount'	=> $creditmemo->getBaseCreditRefunded(),
            'order'		=> $creditmemo->getOrder(),
            'creditmemo'=> $creditmemo,
        );
        $this->creditProcessor->process($creditAccount,$data);
        
        /*Save the refunded credit to order table*/
        $order = $creditmemo->getOrder();
        $order->setCreditRefunded($order->getCreditRefunded() + $creditmemo->getCreditRefunded());
        $order->setBaseCreditRefunded($order->getBaseCreditRefunded() + $creditmemo->getBaseCreditRefunded());
        $order->save();
    }
}
