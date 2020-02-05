<?php

namespace Vnecoms\VendorsCustomWithdrawal\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveWithdrawalData implements ObserverInterface
{
    /**
     * @var \Vnecoms\VendorsCustomWithdrawal\Model\MethodFactory
     */
    protected $methodFactory;

    /**
     * @var \Vnecoms\VendorsCustomWithdrawal\Model\Method\DataFactory
     */
    protected $methodDataFactory;
    
    /**
     * @param \Vnecoms\VendorsCustomWithdrawal\Model\MethodFactory $methodFactory
     * @param \Vnecoms\VendorsCustomWithdrawal\Model\Method\DataFactory $methodDataFactory
     */
    public function __construct(
        \Vnecoms\VendorsCustomWithdrawal\Model\MethodFactory $methodFactory,
        \Vnecoms\VendorsCustomWithdrawal\Model\Method\DataFactory $methodDataFactory
    ) {
        $this->methodFactory = $methodFactory;
        $this->methodDataFactory = $methodDataFactory;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $withdrawal = $observer->getVendorWithdrawal();
        $methodCode = $withdrawal->getMethod();
        $additionalInfo = $withdrawal->getAdditionalInfo();
        $method = $this->methodFactory->create()->load($methodCode, 'code');
        if(!$method->getId()) return;
        $methodData = $this->methodDataFactory->create();
        $methodDataCollection = $methodData->getCollection()
            ->addFieldToFilter('vendor_id',$withdrawal->getVendorId())
            ->addFieldToFilter('method_id',$method->getId());
        
        if($methodDataCollection->count()){
            $methodData = $methodDataCollection->getFirstItem();
        }
        
        $methodData->addData([
            'vendor_id' => $withdrawal->getVendorId(),
            'method_id' => $method->getId(),
            'method_data' => $withdrawal->getAdditionalInfo()
        ])->save();
    }
}
