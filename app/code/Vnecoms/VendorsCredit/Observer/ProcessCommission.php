<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsCredit\Model\CreditProcessor\OrderPayment;
use Vnecoms\VendorsCredit\Model\CreditProcessor\ItemCommission;

class ProcessCommission implements ObserverInterface
{

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * @var \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $_vendorFactory;
    
    /**
     * @var \Vnecoms\Credit\Model\Processor
     */
    protected $_creditProcessor;
    
    /**
     * @var \Vnecoms\Credit\Model\Credit\TransactionFactory
     */
    protected $_transactionFactory;
    
    /**
     * @var \Vnecoms\Credit\Model\CreditFactory
     */
    protected $_creditAccountFactory;
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    
    /**
     * @var \Vnecoms\VendorsCredit\Model\EscrowFactory
     */
    protected $_escrowFactory;
    
     /**
      * Constructor
      *
      * @param \Magento\Framework\Event\ManagerInterface $eventManager
      * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
      * @param \Vnecoms\Vendors\Model\VendorFactory $vendorFactory
      * @param \Magento\Catalog\Model\ProductFactory $productFactory
      * @param \Vnecoms\Credit\Model\Processor $creditProcessor
      * @param \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory
      * @param \Vnecoms\Credit\Model\Credit\TransactionFactory $transactionFactory
      * @param \Vnecoms\VendorsCredit\Model\EscrowFactory $escrowFactory
      */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Vnecoms\Credit\Model\Processor $creditProcessor,
        \Vnecoms\Credit\Model\CreditFactory $creditAccountFactory,
        \Vnecoms\Credit\Model\Credit\TransactionFactory $transactionFactory,
        \Vnecoms\VendorsCredit\Model\EscrowFactory $escrowFactory
    ) {
        $this->_eventManager = $eventManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_vendorFactory = $vendorFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->_escrowFactory = $escrowFactory;
        $this->_creditAccountFactory = $creditAccountFactory;
        $this->_creditProcessor = $creditProcessor;
        $this->_productFactory = $productFactory;
    }
    
    /**
     * Is Enable Escrow Feature
     * @return \Magento\Framework\App\Config\mixed
     */
    public function isEnabledEscrowFeature()
    {
        return $this->_scopeConfig->getValue('vendors/credit/enable_escrow');
    }
    
    /**
     * Add multiple vendor order row for each vendor.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Vnecoms\VendorsSales\Model\Order\Invoice */
        $vendorInvoice = $observer->getVendorInvoice();
        /** @var \Vnecoms\VendorsSales\Model\Order */
        $vendorOrder = $vendorInvoice->getOrder();
        /** @var \Magento\Sales\Model\Order */
        $order = $vendorOrder->getOrder();
        
        /*Don't calculate commission if the invoice has not been paid*/
        if ($vendorInvoice->getState() != \Vnecoms\VendorsSales\Model\Order\Invoice::STATE_PAID) {
            return;
        }
        
        /*Ignore commission calculation for individual payment method*/
        $paymentMethod = $order->getPayment()->getMethod();
        $flag = $this->_scopeConfig->getValue('payment/'.$paymentMethod.'/ignore_commission_calculation');
        if ($flag) {
            return;
        }
            
        /* Add credit to vendor account */
        if (!$vendorOrder->getVendorId()) {
            return;
        }
        
        /* Create escrow transaction*/
        if ($this->isEnabledEscrowFeature()) {
            return $this->createEscrowTransaction($vendorInvoice);
        }
        
        /*Add credit to vendor's credit account and calculate commission*/
        $vendor = $this->_vendorFactory->create();
        $vendor->load($vendorOrder->getVendorId());

        /*Do nothing if the vendor is not exist*/
        if (!$vendor->getId()) {
            return;
        }
        
        /*Return if the transaction is exist.*/
        $trans = $this->_transactionFactory->create()->getCollection()
            ->addFieldToFilter('type', OrderPayment::TYPE)
            ->addFieldToFilter('additional_info', ['like'=>'vendor_invoice|'.$vendorInvoice->getId()]);
        if ($trans->count()) {
            return;
        }

        $creditAccount = $this->_creditAccountFactory->create();
        $creditAccount->loadByCustomerId($vendor->getCustomer()->getId());
        
        $amount = $vendorInvoice->getBaseGrandTotal();

        /*Create transaction to add invoice grandtotal to vendor credit account.*/
        $data = [
            'vendor' => $vendor,
            'type' => OrderPayment::TYPE,
            'amount' => $amount,
            'vendor_order' => $vendorOrder,
            'vendor_invoice' => $vendorInvoice,
            'order' => $order
        ];
        $this->_creditProcessor->process($creditAccount, $data);
        
        /*Calculate commission and create transaction for each item.*/
        foreach ($vendorInvoice->getAllItems() as $item) {
            $orderItem  = $item->getOrderItem();
            if ($orderItem->getParentItemId()) {
                continue;
            }
            $product = $this->_productFactory->create()->load($orderItem->getProductId());
            $trans = $this->_transactionFactory->create()->getCollection()
                ->addFieldToFilter('type', ItemCommission::TYPE)
                ->addFieldToFilter('additional_info', ['like'=>'invoice_item|'.$item->getId().'%']);
            if ($trans->count()) {
                continue;
            }
            $fee = $item->getCommission();
            /*Do nothing if the fee is zero*/
            if ($fee <= 0) {
                continue;
            }
            
            $additionalDescription = $item->getCommissionDescription();
            
            $data = [
                'vendor' => $vendor,
                'type' => ItemCommission::TYPE,
                'amount' => $fee,
                'invoice_item' => $item,
                'order' => $order,
                'vendor_invoice' => $vendorInvoice,
                'additional_description' => $additionalDescription,
            ];
            
            $this->_creditProcessor->process($creditAccount, $data);
        }
        return $this;
    }
    
    /**
     * Create escrow transaction
     * @param \Vnecoms\VendorsSales\Model\Order\Invoice $vendorInvoice
     */
    public function createEscrowTransaction(\Vnecoms\VendorsSales\Model\Order\Invoice $vendorInvoice)
    {
        /*Add credit to vendor's credit account and calculate commission*/
        $vendor = $this->_vendorFactory->create();
        $vendor->load($vendorInvoice->getVendorId());
        
        /*Do nothing if the vendor is not exist*/
        if (!$vendor->getId()) {
            return;
        }
        
        /*Return if the transaction is exist.*/
        $escrows = $this->_escrowFactory->create()->getCollection()
            ->addFieldToFilter('relation_id', $vendorInvoice->getId());
        if ($escrows->count()) {
            return;
        }
        
        $amount         = $vendorInvoice->getBaseGrandTotal();
        
        $data = [
            'vendor_id'      => $vendor->getId(),
            'relation_id'    => $vendorInvoice->getId(),
            'amount'         => $amount,
            'status'         => \Vnecoms\VendorsCredit\Model\Escrow::STATUS_PENDING,
            'additional_info'    => 'order|'.$vendorInvoice->getOrder()->getId().'|invoice|'.$vendorInvoice->getId(),
        ];
        
        $escrow = $this->_escrowFactory->create();
        $escrow->setData($data)->save();
        
        /* Send notification email*/
    }
}
