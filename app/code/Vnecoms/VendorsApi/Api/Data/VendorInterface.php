<?php

namespace Vnecoms\VendorsApi\Api\Data;

interface VendorInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID            = 'id';
    const CUSTOMER_ID   = 'customer_id';
    const WEBSITE_ID    = 'website_id';
    const INCREMENT_ID  = 'increment_id';
    const VENDOR_ID     = 'vendor_id';
    const EMAIL         = 'email';
    const FIRSTNAME     = 'firstname';
    const LASTNAME      = 'lastname';
    const MIDDLENAME    = 'middlename';
    const PREFIX        = 'prefix';
    const SUFFIX        = 'suffix';
    const CREATED_AT    = 'created_at';
    const UPDATED_AT    = 'updated_at';
    const GROUP_ID      = 'group_id';
    const GROUP_NAME    = 'group_name';
    const STATUS        = 'status';
    const STATUS_LABEL  = 'status_label';
    const CITY          = 'city';
    const COMPANY       = 'company';
    const COUNTRY_ID    = 'country_id';
    const FAX           = 'fax';
    const POSTCODE      = 'postcode';
    const REGION        = 'region';
    const REGION_CODE   = 'region_code';
    const REGION_ID     = 'region_id';
    const STREET        = 'street';
    const TELEPHONE     = 'telephone';
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
     * Get vendor id
     * 
     * @return string
     */
    public function getVendorId();
    
    /**
     * Set vendor identifier
     *
     * @param string $vendorId
     * @return $this
     */
    public function setVendorId($vendorId);
    
    /**
     * Get email
     *
     * @return string
     */
    public function getEmail();
    
    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

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
    
    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();
    
    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
    
    /**
     * Get group id
     *
     * @return int
     */
    public function getGroupId();
    
    /**
     * Set group id
     *
     * @param int $groupId
     * @return $this
     */
    public function setGroupId($groupId);
    
    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();
    
    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);
    
    /**
     * Get city
     *
     * @return string|null
     */
    public function getCity();
    
    /**
     * Set City
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city);
    
    /**
     * Get company
     *
     * @return string|null
     */
    public function getCompany();
    
    /**
     * Set Company
     *
     * @param string $company
     * @return $this
     */
    public function setCompany($company);
    
    /**
     * Get country id
     *
     * @return string|null
     */
    public function getCountryId();
    
    /**
     * Set country id
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryId($countryId);
    
    /**
     * Get Fax
     *
     * @return string|null
     */
    public function getFax();
    
    /**
     * Set Fax
     *
     * @param string $fax
     * @return $this
     */
    public function setFax($fax);
    
    /**
     * Get post code
     *
     * @return string|null
     */
    public function getPostcode();
    
    /**
     * Set post code
     *
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode);
    
    /**
     * Retrieve region name
     *
     * @return string|null
     */
    public function getRegion();
    
    /**
     * Set region
     *
     * @param string $region
     * @return $this
    */
    public function setRegion($region);
    
    /**
     * @return int|null
    */
    public function getRegionId();
    
    /**
     * Set region Id
     *
     * @param int $regionId
     * @return $this
    */
    public function setRegionId($regionId);
    
    /**
     * Get street
     *
     * @return string|null
     */
    public function getStreet();
    
    /**
     * Set street
     *
     * @param string $street
     * @return $this
     */
    public function setStreet($street);
    
    /**
     * Get group id
     *
     * @return string|null
     */
    public function getTelephone();
    
    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone);
    
    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId();
    
    /**
     * Set customer Id
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);
    
    /**
     * get first name
     * @return string
     */
    public function getFirstname();
    
    /**
     * Set first name
     *
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname);
    
    /**
     * get last name
     * @return string
     */
    public function getLastname();
    
    /**
     * Set last name
     *
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname);
    
    /**
     * get middle name
     * @return string
     */
    public function getMiddlename();
    
    /**
     * Set middle name
     *
     * @param string $middleName
     * @return $this
     */
    public function setMiddlename($middleName);
    
    /**
     * get group name
     * @return string
     */
    public function getGroupName();
    
    /**
     * Set group name
     *
     * @param string $groupName
     * @return $this
     */
    public function setGroupName($groupName);
    
    /**
     * get customer name
     * @return string
     */
    public function getName();
    
    /**
     * Get status Label
     *
     * @return string
     */
    public function getStatusLabel();
    
    /**
     * @return string|null
     */
    public function getCountry();
    
    /**
     * Get country Name
     *
     * @param string $locale
     * @return string
     */
    public function getCountryName($locale = null);

    /**
     * Return 2 letter state code if available, otherwise full region name
     *
     * @return string
     */
    public function getRegionCode();

}
