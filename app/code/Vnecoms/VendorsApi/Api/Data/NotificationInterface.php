<?php

namespace Vnecoms\VendorsApi\Api\Data;

interface NotificationInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID                = 'notification_id';
    const TYPE              = 'type';
    const MESSAGE           = 'message';
    const ADDITIONAL_INFO   = 'additional_info';
    const IS_READ           = 'is_read';
    const IS_REACHED        = 'is_reached';
    const IS_HIDDEN         = 'is_hidden';
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
     * Get message
     *
     * @return string
     */
    public function getMessage();
    
    /**
     * Set transaction amount
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
    
    /**
     * Get additional info
     *
     * @return string[]
     */
    public function getAdditionalInfo();
    
    /**
     * Set additional info
     *
     * @param string[] $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo($additionalInfo);
    
    /**
     * Get is read
     *
     * @return bool
     */
    public function getIsRead();
    
    /**
     * Set is read
     *
     * @param bool $isRead
     * @return $this
     */
    public function setIsRead($isRead);
    
    /**
     * Get is reached
     *
     * @return bool
     */
    public function getIsReached();
    
    /**
     * Set is reached
     *
     * @param bool $isReached
     * @return $this
    */
    public function setIsReached($isReached);
    
    /**
     * Get is hidden
     *
     * @return bool
     */
    public function getIsHidden();
    
    /**
     * Set is hidden
     *
     * @param bool $isHidden
     * @return $this
    */
    public function setIsHidden($isHidden);
    
    
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
