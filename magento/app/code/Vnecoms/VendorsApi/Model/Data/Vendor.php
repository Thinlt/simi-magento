<?php

namespace Vnecoms\VendorsApi\Model\Data;


/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Vendor extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Vnecoms\VendorsApi\Api\Data\VendorInterface
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
     * Get vendor id
     *
     * @return string
    */
    public function getVendorId(){
        return $this->_get(self::VENDOR_ID);
    }
    
    /**
     * Set vendor identifier
     *
     * @param string $vendorId
     * @return $this
    */
    public function setVendorId($vendorId){
        return $this->setData(self::VENDOR_ID, $vendorId);
    }
    
    /**
     * Get email
     *
     * @return string
     */
    public function getEmail(){
        return $this->_get(self::EMAIL);
    }
    
    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email){
        return $this->setData(self::EMAIL, $email);
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
    
    /**
     * Get updated at
     *
     * @return string
    */
    public function getUpdatedAt(){
        return $this->_get(self::UPDATED_AT);
    }
    
    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
    */
    public function setUpdatedAt($updatedAt){
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
    
    /**
     * Get group id
     *
     * @return int
    */
    public function getGroupId(){
        return $this->_get(self::GROUP_ID);
    }
    
    /**
     * Set group id
     *
     * @param int $groupId
     * @return $this
    */
    public function setGroupId($groupId){
        return $this->setData(self::GROUP_ID, $groupId);
    }
    
    /**
     * Get status
     *
     * @return int
    */
    public function getStatus(){
        return $this->_get(self::STATUS);
    }
    
    /**
     * Set status
     *
     * @param int $status
     * @return $this
    */
    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }
    
    /**
     * Get city
     *
     * @return string|null
    */
    public function getCity(){
        return $this->_get(self::CITY);
    }
    
    /**
     * Set City
     *
     * @param string $city
     * @return $this
    */
    public function setCity($city){
        return $this->setData(self::CITY, $city);
    }
    
    /**
     * Get company
     *
     * @return string|null
    */
    public function getCompany(){
        return $this->_get(self::COMPANY);
    }
    
    /**
     * Set Company
     *
     * @param string $company
     * @return $this
    */
    public function setCompany($company){
        return $this->setData(self::COMPANY, $company);
    }
    
    /**
     * Get country id
     *
     * @return string|null
    */
    public function getCountryId(){
        return $this->_get(self::COUNTRY_ID);
    }
    
    /**
     * Set country id
     *
     * @param string $countryId
     * @return $this
    */
    public function setCountryId($countryId){
        return $this->setData(self::COUNTRY_ID, $countryId);
    }
    
    /**
     * Get Fax
     *
     * @return string|null
    */
    public function getFax(){
        return $this->_get(self::FAX);
    }
    
    /**
     * Set Fax
     *
     * @param string $fax
     * @return $this
    */
    public function setFax($fax){
        return $this->setData(self::FAX, $fax);
    }
    
    /**
     * Get post code
     *
     * @return string|null
    */
    public function getPostcode(){
        return $this->_get(self::POSTCODE);
    }
    
    /**
     * Set post code
     *
     * @param string $postcode
     * @return $this
    */
    public function setPostcode($postcode){
        return $this->setData(self::POSTCODE, $postcode);
    }
    
    /**
     * Retrieve region name
     *
     * @return string|null
    */
    public function getRegion(){
        return $this->_get(self::REGION);
    }
    
    /**
     * Set region
     *
     * @param string $region
     * @return $this
    */
    public function setRegion($region){
        return $this->setData(self::REGION, $region);
    }
    
    /**
     * @return int|null
    */
    public function getRegionId(){
        return $this->_get(self::REGION_ID);
    }
    
    /**
     * Set region Id
     *
     * @param int $regionId
     * @return $this
    */
    public function setRegionId($regionId){
        return $this->setData(self::REGION_ID, $regionId);
    }
    
    /**
     * Get street
     *
     * @return string|null
    */
    public function getStreet(){
        return $this->_get(self::STREET);
    }
    
    /**
     * Set street
     *
     * @param string $street
     * @return $this
    */
    public function setStreet($street){
        return $this->setData(self::STREET, $street);
    }
    
    /**
     * Get group id
     *
     * @return string|null
    */
    public function getTelephone(){
        return $this->_get(self::TELEPHONE);
    }
    
    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
    */
    public function setTelephone($telephone){
        return $this->setData(self::TELEPHONE, $telephone);
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
     * Set customer Id
     *
     * @param string $customerId
     * @return $this
    */
    public function setCustomerId($customerId){
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
    
    /**
     * get first name
     * @return string
    */
    public function getFirstname(){
        return $this->_get(self::FIRSTNAME);
    }
    
    /**
     * Set first name
     *
     * @param string $firstname
     * @return $this
    */
    public function setFirstname($firstname){
        return $this->setData(self::FIRSTNAME, $firstname);
    }
    
    /**
     * get last name
     * @return string
    */
    public function getLastname(){
        return $this->_get(self::LASTNAME);
    }
    
    /**
     * Set last name
     *
     * @param string $lastname
     * @return $this
    */
    public function setLastname($lastname){
        return $this->setData(self::LASTNAME, $lastname);
    }
    
    /**
     * get middle name
     * @return string
    */
    public function getMiddlename(){
        return $this->_get(self::MIDDLENAME);
    }
    
    /**
     * Set middle name
     *
     * @param string $middleName
     * @return $this
    */
    public function setMiddlename($middleName){
        return $this->setData(self::MIDDLENAME, $middleName);
    }
    
    /**
     * get group name
     * @return string
     */
    public function getGroupName(){
        return $this->_get(self::GROUP_NAME);
    }
    
    /**
     * Set group name
     *
     * @param string $groupName
     * @return $this
     */
    public function setGroupName($groupName){
        return $this->setData(self::GROUP_NAME, $groupName);
    }
    
    /**
     * get customer name
     * @return string
    */
    public function getName(){
        return $this->getFirstname().' '. $this->getLastname();
    }
    
    /**
     * Get status Label
     *
     * @return string
    */
    public function getStatusLabel(){
        if (!$this->_get(self::STATUS_LABEL)) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $source = $om->create('Vnecoms\Vendors\Model\Source\Status');
            $options = $source->getOptionArray();
            if (isset($options[$this->getStatus()])) {
                $this->setData(self::STATUS_LABEL, $options[$this->getStatus()]);
            }
        }
        return $this->_get(self::STATUS_LABEL);
    }
    
    /**
     * @return string|null
    */
    public function getCountry(){
        return $this->getCountryId();
    }
    
    /**
     * Get country Name
     *
     * @param string $locale
     * @return string
    */
    public function getCountryName($locale = null){
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $country = $om->create('Magento\Directory\Model\Country');
        $country->load($this->getCountryId());
        return $country->getName($locale);
    }
    
    /**
     * Return 2 letter state code if available, otherwise full region name
     *
     * @return string
    */
    public function getRegionCode(){
        $regionId = $this->_get(self::REGION_ID);
        $region = $this->_get(self::REGION);
        
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $regionModel = $om->create('Magento\Directory\Model\Region');
        if (!$regionId && is_numeric($region)) {
            $regionModel->load($region);
            if ($regionModel->getCountryId() == $this->getCountryId()) {
                $this->setData(self::REGION, $regionModel->getName());
                $this->setData(self::REGION_ID, $region);
            }
        } elseif ($regionId) {
            $regionModel->load($regionId);
            if ($regionModel->getCountryId() == $this->getCountryId()) {
                $this->setData(self::REGION, $regionModel->getName());
            }
        }
        
        return $this->_get(self::REGION);
    }
}
