<?php

namespace Vnecoms\VendorsApi\Model\Data\Credit;


/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Transaction extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Vnecoms\VendorsApi\Api\Data\Credit\TransactionInterface
{
    /**
     * Get id
     *
     * @return int|null
     */
    public function getId(){
        return $this->_get(self::ID);
    }
    
    /**
     * Set vendor id
     *
     * @param int $id
     * @return $this
    */
    public function setId($id){
        return $this->setData(self::ID, $id);
    }
    
    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId(){
        return $this->_get(self::CUSTOMER_ID);
    }
    
    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId){
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
    
    /**
     * Get type
     *
     * @return string
     */
    public function getType(){
        return $this->_get(self::TYPE);
    }
    
    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type){
        return $this->setData(self::TYPE, $type);
    }
    
    /**
     * Get transaction amount
     *
     * @return float
     */
    public function getAmount(){
        return $this->_get(self::AMOUNT);
    }
    
    /**
     * Set transaction amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount){
        return $this->setData(self::AMOUNT, $amount);
    }
    
    /**
     * Get balance
     *
     * @return float
     */
    public function getBalance(){
        return $this->_get(self::BALANCE);
    }
    
    /**
     * Set balance
     *
     * @param float $balance
     * @return $this
     */
    public function setBalance($balance){
        return $this->setData(self::BALANCE, $balance);
    }
    
    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription(){
        return $this->_get(self::DESCRIPTION);
    }
    
    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description){
        return $this->setData(self::DESCRIPTION, $description);
    }
    
    /**
     * Get additional info
     *
     * @return string|null
     */
    public function getAdditionalInfo(){
        return $this->_get(self::ADDITIONAL_INFO);
    }
    
    /**
     * Set additional info
     *
     * @param string $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo($additionalInfo){
        return $this->setData(self::ADDITIONAL_INFO, $additionalInfo);
    }
    
    /**
     * Get created at
     *
     * @return string
    */
    public function getCreatedAt(){
        return $this->_get(self::CREATED_AT);
    }
    
    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
    */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }
    
}
