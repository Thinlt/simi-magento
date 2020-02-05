<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * @method int getCustomerId();
 * @method int getCredit();
 */
class Credit extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'credit';
    
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;
    
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;
    
    /**
     * @var \Vnecoms\Credit\Model\ResourceModel\Credit\Transaction\CollectionFactory
     */
    protected $_transactionCollectionFactory;
    
    /**
     * @var \Vnecoms\Credit\Model\ResourceModel\Credit\Transaction\Collection
     */
    protected $_transactionCollection;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Credit\Model\ResourceModel\Credit');
    }
    
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Vnecoms\Credit\Model\ResourceModel\Credit $resource
     * @param \Vnecoms\Credit\Model\ResourceModel\Credit\Collection $resourceCollection
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Vnecoms\Credit\Model\ResourceModel\Credit\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Vnecoms\Credit\Model\ResourceModel\Credit $resource,
        \Vnecoms\Credit\Model\ResourceModel\Credit\Collection $resourceCollection,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Vnecoms\Credit\Model\ResourceModel\Credit\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_transactionCollectionFactory = $transactionCollectionFactory;
        $this->_customerFactory = $customerFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    
    /**
     * Add credit
     * @param float $amount
     * @return Vnecoms\Credit\Model\Credit
     */
    public function addCredit($amount){
        $this->setCredit($this->getCredit() + $amount);
        $this->save();
        return $this;
    }
    
    /**
     * Subtract credit
     * @param float $amount
     */
    public function subtractCredit($amount){
        if($this->getCredit() < $amount)
            throw new LocalizedException(__('You do not have enough credit amount to do this action'));
        
        $this->setCredit($this->getCredit() - $amount);
        $this->save();
        return $this;
    }
    
    /**
     * Get customer object
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer(){
        if(!$this->_customer){
            $this->_customer = $this->_customerFactory->create();
            $this->_customer->load($this->getCustomerId());
        }
        
        return $this->_customer;
    }
    
    /**
     * Get transaction collection
     * @return \Vnecoms\Credit\Model\ResourceModel\Credit\Transaction\Collection
     */
    public function getTransactionCollection(){
        if(!$this->_transactionCollection){
            $this->_transactionCollection = $this->_transactionCollectionFactory->create();
            $this->_transactionCollection->addFieldToFilter('customer_id',$this->getCustomerId());
        }
        
        return $this->_transactionCollection;
    }
    
    /**
     * Format base currency
     * @param float $credit
     */
    public function formatBaseCurrency($credit){
        $storeId = $this->getCustomer()->getWebsiteId();
        $store = $this->_storeManager->getWebsite($storeId);
        $baseCurrency = $store->getBaseCurrency();
        
        return $baseCurrency->formatPrecision($credit,2,[],false);
    }
    
    /**
     * Load credit account by customer id
     * @param int $customerId
     * @return \Vnecoms\Credit\Model\Credit
     */
    public function loadByCustomerId($customerId){
        $this->load($customerId,'customer_id');
        /*If the customer credit account is not exist just create new one*/
        if(!$this->getId()){
            /*Check if the customer id is exist*/
            $customer = $this->_customerFactory->create();
            $customer->load($customerId);
            if(!$customer->getId()) throw new LocalizedException(__("Customer Id is not valid"));
            /*Add new customer credit account*/
            $this->setData([
               'customer_id' => $customerId,
                'credit' => 0, 
            ])->setId(null)->save();
        }
        return $this;
    }
}
