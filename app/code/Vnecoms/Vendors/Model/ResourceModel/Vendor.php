<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\Vendors\Model\ResourceModel;

/**
 * Cms page mysql resource
 */
class Vendor extends \Magento\Eav\Model\Entity\AbstractEntity
{
    /**
     * @var \Magento\Framework\Validator\Factory
     */
    protected $_validatorFactory;
    
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @param \Magento\Eav\Model\Entity\Context $context
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite $entityRelationComposite
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Validator\Factory $validatorFactory
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Context $context,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite $entityRelationComposite,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Validator\Factory $validatorFactory,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->_scopeConfig = $scopeConfig;
        $this->_validatorFactory = $validatorFactory;
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->setType('vendor');
        //$this->setConnection('customer_read', 'customer_write');
    }
    
    /**
     * After Load Entity process
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\DataObject $object)
    {
        $table = $this->getTable('ves_vendor_user');
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_user'),
            'customer_id'
        )->where(
            'vendor_id = :vendor_id'
        )->where(
            'is_super_user = :is_super_user'
        );
        $bind = [
            'vendor_id' => $object->getId(),
            'is_super_user' => 1
        ];
        $customerId = $connection->fetchOne($select, $bind);

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        
        $customer = $om->create('Magento\Customer\Model\Customer');
        $customer->load($customerId);
        $this->_setCustomerData($object, $customer);
        
        return parent::_afterLoad($object);
    }
    
    protected function _setCustomerData(\Magento\Framework\DataObject $object, \Magento\Customer\Model\Customer $customer){
        $object->setCustomer($customer);
        $object->setData('firstname',$customer->getData('firstname'));
        $object->setData('middlename',$customer->getData('middlename'));
        $object->setData('lastname',$customer->getData('lastname'));
        $object->setData('email',$customer->getData('email'));
        return $this;
    }
    
    /**
     * After save the vendor object
     * @see \Magento\Eav\Model\Entity\AbstractEntity::_afterSave()
     */
    protected function _afterSave(\Magento\Framework\DataObject $object){
        $table = $this->getTable('ves_vendor_user');
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_user'),
            'customer_id'
        )->where(
            'vendor_id = :vendor_id'
        )->where(
            'is_super_user = :is_super_user'
        );
        $bind = [
            'vendor_id' => $object->getId(),
            'is_super_user' => 1
        ];
        $customerId = $connection->fetchOne($select, $bind);
        
        if(!$customerId){
            $customer = $object->getCustomer();
            if(!$customer || !$customer->getId()) throw new \Exception(__("Customer object is not set at line %1 in file %2",__LINE__, __FILE__));
            
            $sql = "INSERT INTO $table(customer_id, vendor_id, is_super_user) VALUES({$customer->getId()},{$object->getId()},1)";
            $connection->query($sql);
        }
        return parent::_afterSave($object);
    }
    
    /**
     * Load vendor by customer
     * @param \Vnecoms\Vendors\Model\Vendor $object
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function loadByCustomer(
        \Vnecoms\Vendors\Model\Vendor $object,
        \Magento\Customer\Model\Customer $customer
    ){
        $table = $this->getTable('ves_vendor_user');
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_user'),
            'vendor_id'
        )->where(
            'customer_id = :customer_id'
        );
        $bind = [
            'customer_id' => $customer->getId(),
        ];
        $vendorId = $connection->fetchOne($select,$bind);
        if($vendorId){
            $object->load($vendorId);
            if($object->getEntityId()) $this->_setCustomerData($object, $customer);
        }
    }
    
    /**
     * Load vendor by identifier
     * 
     * @param \Vnecoms\Vendors\Model\Vendor $object
     * @param unknown $vendorId
     */
    public function loadByIdentifier(
        \Vnecoms\Vendors\Model\Vendor $object,
        $vendorId
    ){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_entity'),
            'entity_id'
        )->where(
            'vendor_id = :vendor_id'
        );
        $bind = [
            'vendor_id' => $vendorId,
        ];

        $vendorId = $connection->fetchOne($select,$bind);
        
        if($vendorId){
            $object->load($vendorId);
        }
    }
    
    
    /**
     * (non-PHPdoc)
     * 
     * @param \Magento\Framework\DataObject $object
     * @see \Magento\Eav\Model\Entity\AbstractEntity::validate()
     */
    public function validate($vendor)
    {
        $table = $this->getTable('ves_vendor_entity');
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getEntityTable(),
            $this->getEntityIdField()
        )->where(
            'vendor_id = :vendor_id'
        );
        $bind = [
            'vendor_id' => $vendor->getVendorId(),
        ];
        
        $existVendorId = $connection->fetchOne($select,$bind);
        
        if ($existVendorId && ($vendor->getId() != $existVendorId)) {
            return ['vendor_id' => __("Vendor id is already in used.")];
        }
        
        return parent::validate($vendor);
    }
    
    /**
     * Get Related Customer Id By Vendor Id
     * @param int $vendorId
     * @return string
     */
    public function getRelatedCustomerIdByVendorId($vendorId){
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            $this->getTable('ves_vendor_user'),
            'customer_id'
        )->where(
            'vendor_id = :vendor_id'
        )->where(
            'is_super_user = :is_super_user'
        );
        $bind = [
            'vendor_id' => $vendorId,
            'is_super_user' => 1,
        ];
        $customerId = $connection->fetchOne($select,$bind);
        
        return $customerId;
    }
}
