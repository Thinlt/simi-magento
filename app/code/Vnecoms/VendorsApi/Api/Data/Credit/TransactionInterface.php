<?php

namespace Vnecoms\VendorsApi\Api\Data\Credit;

interface TransactionInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID                = 'transaction_id';
    const CUSTOMER_ID       = 'customer_id';
    const TYPE              = 'type';
    const AMOUNT            = 'amount';
    const BALANCE           = 'balance';
    const DESCRIPTION       = 'description';
    const ADDITIONAL_INFO   = 'additional_info';
    const CREATED_AT        = 'created_at';
    
    /**#@-*/
    
    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();
    
    /**
     * Set vendor id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);
    
    /**
     * Get customer id
     * 
     * @return int
     */
    public function getCustomerId();
    
    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);
    
    /**
     * Get type
     *
     * @return string
     */
    public function getType();
    
    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get transaction amount
     *
     * @return float
     */
    public function getAmount();
    
    /**
     * Set transaction amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount);
    
    /**
     * Get balance
     *
     * @return float
     */
    public function getBalance();
    
    /**
     * Set balance
     *
     * @param float $balance
     * @return $this
     */
    public function setBalance($balance);
    
    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();
    
    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);
    
    /**
     * Get additional info
     *
     * @return string|null
     */
    public function getAdditionalInfo();
    
    /**
     * Set additional info
     *
     * @param string $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo($additionalInfo);
    
    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();
    
    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

}
