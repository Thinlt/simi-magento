<?php

namespace Vnecoms\VendorsCustomWithdrawal\Plugin;

use Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method\CollectionFactory;
use Magento\Framework\ObjectManagerInterface;

class Helper
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
    
    /**
     * @param CollectionFactory $collectionFactory
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ObjectManagerInterface $objectManager
    ) {
        $this->collectionFactory    = $collectionFactory;
        $this->objectManager        = $objectManager;
    }

    /**
     * @param \Vnecoms\VendorsCredit\Helper\Data $subject
     * @param array $result
     * @return array
     */
    public function afterGetWithdrawalMethods(
        \Vnecoms\VendorsCredit\Helper\Data $subject,
        $result
    ) {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('is_enabled', 1);
        
        foreach($collection as $method){
            $withdrawalMethod = $this->objectManager
                ->create('Vnecoms\VendorsCustomWithdrawal\Model\Withdrawal\Method\Custom')
                ->setMethodObj($method);
            $result[$method->getCode()] = $withdrawalMethod;
        } 
        return $result;
    }
}
