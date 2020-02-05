<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model;

/**
 * @method \Magento\Customer\Model\Customer getCustomer();
 * @method string getFirstname();
 * @method string getLastname();
 * @method string getMiddlename();
 * @method string getEmail();
 */
class Vendor extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_PENDING    = 1;
    const STATUS_APPROVED   = 2;
    const STATUS_DISABLED   = 3;
    const STATUS_EXPIRED    = 4;
    
    const ENTITY = 'vendor';

    const XML_PATH_REGISTER_EMAIL_TEMPLATE      = 'vendors/create_account/email_template';
    const XML_PATH_REGISTER_EMAIL_IDENTITY      = 'vendors/create_account/email_identity';
    const XML_PATH_ACTIVE_EMAIL_TEMPLATE        = 'vendors/create_account/email_template_approved';

    const XML_PATH_REGISTER_EMAIL_TEMPLATE_ADMIN      = 'vendors/admin_notification/email_template_pending';
    const XML_PATH_ACTIVE_EMAIL_TEMPLATE_ADMIN        = 'vendors/admin_notification/email_template_approved';
    const XML_PATH_ADMIN_EMAIL_TO        = 'vendors/admin_notification/email_to';
    /**
     * Vendor Group Object
     * @var \Vnecoms\Vendors\Model\Group
     */
    protected $_vendor_group;
    
    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'vendor';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'vendor';
    
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_config;
    
    /**
     * Directory country models
     *
     * @var \Magento\Directory\Model\Country[]
     */
    protected static $_countryModels = [];
    
    /**
     * Directory region models
     *
     * @var \Magento\Directory\Model\Region[]
     */
    protected static $_regionModels = [];
    
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;
    
    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var \Vnecoms\Vendors\Helper\Email
     */
    protected $_emailHelper;


    /**
     * store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Vnecoms\Vendors\Model\ResourceModel\Vendor $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Vnecoms\Vendors\Model\ResourceModel\Vendor $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_config = $config;
        $this->_regionFactory = $regionFactory;
        $this->_countryFactory = $countryFactory;
        $this->_storeManager = $storeManager;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }
    
    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Vnecoms\Vendors\Model\ResourceModel\Vendor');
    }
    
    
    /**
     * Get Vendor Group
     *
     * @return \Vnecoms\Vendors\Model\Group
     */
    public function getGroup()
    {
        if (!$this->_vendor_group) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_vendor_group = $om->create('Vnecoms\Vendors\Model\Group');
            $this->_vendor_group->load($this->getGroupId());
        }
        return $this->_vendor_group;
    }
    
    /**
     * get customer name
     * @return string
     */
    public function getName()
    {
        return $this->getFirstname().($this->getMiddlename()?' '.$this->getMiddlename():'')." ".$this->getLastname();
    }
    
    /**
     * Load by customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function loadByCustomer(\Magento\Customer\Model\Customer $customer)
    {
        $this->getResource()->loadByCustomer($this, $customer);
        return $this;
    }
    
    /**
     * Load by identifier
     *
     * @param string $vendorId
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function loadByIdentifier($vendorId)
    {
        $this->getResource()->loadByIdentifier($this, $vendorId);
        return $this;
    }
    
    /**
     * Load by vendor Id
     *
     * @param string $vendorId
     * @return Ambigous <\Vnecoms\Vendors\Model\Vendor, \Vnecoms\Vendors\Model\Vendor>
     */
    public function loadByVendorId($vendorId)
    {
        return $this->loadByIdentifier($vendorId);
    }
    
    /**
     * Validate Vendor Data
     *
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function validate()
    {
        $errors = [];
        if (!\Zend_Validate::is(trim($this->getVendorId()), 'NotEmpty')) {
            $errors[] = __('Please enter vendor id.');
        }
        
        $entityType = $this->_config->getEntityType('vendor');
        
        $attribute = $this->_config->getAttribute($entityType, 'street');
        if ($attribute->getIsRequired() && '' == trim($this->getStreet())) {
            $errors[] = __('Please enter address.');
        }
        
        $attribute = $this->_config->getAttribute($entityType, 'city');
        if ($attribute->getIsRequired() && '' == trim($this->getCity())) {
            $errors[] = __('Please enter city.');
        }
      
        $attribute = $this->_config->getAttribute($entityType, 'country');
        if ($attribute->getIsRequired() && '' == trim($this->getCountry())) {
            $errors[] = __('Please enter country.');
        }
        $attribute = $this->_config->getAttribute($entityType, 'telephone');
        if ($attribute->getIsRequired() && '' == trim($this->getTelephone())) {
            $errors[] = __('Please enter a phone number');
        }
        
        $errors1 = $this->getResource()->validate($this);
        if (is_array($errors1)) {
            $errors = array_merge($errors, $errors1);
        }
        
        $transport = new \Magento\Framework\DataObject(
            ['errors' => $errors]
        );
        $this->_eventManager->dispatch('vendor_validate', ['vendor' => $this, 'transport' => $transport]);
        $errors = $transport->getErrors();
        
        if (empty($errors)) {
            return true;
        }
        
        return $errors;
    }
    
    /**
     * Get status Label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        if (!$this->getData('status_label')) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $source = $om->create('Vnecoms\Vendors\Model\Source\Status');
            $options = $source->getOptionArray();
            if (isset($options[$this->getStatus()])) {
                $this->setData('status_label', $options[$this->getStatus()]);
            }
        }
        return $this->getData('status_label');
    }
    
    /**
     * @return int
     */
    public function getCountry()
    {
        $country = $this->getCountryId();
        return $country ? $country : $this->getData('country');
    }
    
    /**
     * Retrieve country model
     *
     * @return \Magento\Directory\Model\Country
     */
    public function getCountryModel()
    {
        if (!isset(self::$_countryModels[$this->getCountryId()])) {
            $country = $this->_createCountryInstance();
            $country->load($this->getCountryId());
            self::$_countryModels[$this->getCountryId()] = $country;
        }
    
        return self::$_countryModels[$this->getCountryId()];
    }
    
    /**
     * Get country Name
     *
     * @param string $locale
     * @return stirng
     */
    public function getCountryName($locale = null)
    {
        return $this->getCountryModel()->getName($locale);
    }

    /**
     * Retrieve country model
     *
     * @param int|null $regionId
     * @return \Magento\Directory\Model\Region
     */
    public function getRegionModel($regionId = null)
    {
        if ($regionId === null) {
            $regionId = $this->getRegionId();
        }
    
        if (!isset(self::$_regionModels[$regionId])) {
            $region = $this->_createRegionInstance();
            $region->load($regionId);
            self::$_regionModels[$regionId] = $region;
        }
    
        return self::$_regionModels[$regionId];
    }
    
    /**
     * Retrieve region name
     *
     * @return string
     */
    public function getRegion()
    {
        $regionId = $this->getData('region_id');
        $region = $this->getData('region');

        if (!$regionId && is_numeric($region)) {
            if ($this->getRegionModel($region)->getCountryId() == $this->getCountryId()) {
                $this->setData('region', $this->getRegionModel($region)->getName());
                $this->setData('region_id', $region);
            }
        } elseif ($regionId) {
            if ($this->getRegionModel($regionId)->getCountryId() == $this->getCountryId()) {
                $this->setData('region', $this->getRegionModel($regionId)->getName());
            }
        } elseif (is_string($region)) {
            $this->setData('region', $region);
        }

        return $this->getData('region');
    }
    
    /**
     * Return 2 letter state code if available, otherwise full region name
     *
     * @return string
     */
    public function getRegionCode()
    {
        $regionId = $this->getData('region_id');
        $region = $this->getData('region');
    
        if (!$regionId && is_numeric($region)) {
            if ($this->getRegionModel($region)->getCountryId() == $this->getCountryId()) {
                $this->setData('region_code', $this->getRegionModel($region)->getCode());
            }
        } elseif ($regionId) {
            if ($this->getRegionModel($regionId)->getCountryId() == $this->getCountryId()) {
                $this->setData('region_code', $this->getRegionModel($regionId)->getCode());
            }
        } elseif (is_string($region)) {
            $this->setData('region_code', $region);
        }
        return $this->getData('region_code');
    }
    
    
    /**
     * @return int
     */
    public function getRegionId()
    {
        $regionId = $this->getData('region_id');
        $region = $this->getData('region');
        if (!$regionId) {
            if (is_numeric($region)) {
                $this->setData('region_id', $region);
                //@TODO method unsRegion() is neither defined in abstract model nor in it's children
                $this->unsRegion();
            } else {
                $regionModel = $this->_createRegionInstance()->loadByCode(
                    $this->getRegionCode(),
                    $this->getCountryId()
                );
                $this->setData('region_id', $regionModel->getId());
            }
        }
        return $this->getData('region_id');
    }
    
    
    /**
     * @return \Magento\Directory\Model\Region
     */
    protected function _createRegionInstance()
    {
        return $this->_regionFactory->create();
    }
    
    /**
     * @return \Magento\Directory\Model\Country
     */
    protected function _createCountryInstance()
    {
        return $this->_countryFactory->create();
    }
    /**
     * Get Email Helper
     *
     * @return \Vnecoms\Vendors\Helper\Email
     */
    public function getEmailHelper()
    {
        if (!$this->_emailHelper) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_emailHelper = $om->create('Vnecoms\Vendors\Helper\Email');
        }
        return $this->_emailHelper;
    }
    /**
     * Send email with new account related information
     *
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendNewAccountEmail($type = 'registered')
    {
        $types = [
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE,  // welcome email, when confirmation is disabled
            'active' => self::XML_PATH_ACTIVE_EMAIL_TEMPLATE,  // Active email, when Vendor is active
        ];

        $adminTypes = [
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE_ADMIN,  // welcome email, when confirmation is disabled
            'active' => self::XML_PATH_ACTIVE_EMAIL_TEMPLATE_ADMIN,  // Active email, when Vendor is active
        ];

        $om = \Magento\Framework\App\ObjectManager::getInstance();

        if ($this->getCustomer()) {
             $customer_id = $this->getCustomer()->getId();
        } else {
             $customer_id = $this->getData("vendor_user_customer_id");
        }
        if (!$customer_id) {
            return $this;
        }

        $customer = $om->create('Magento\Customer\Model\Customer')->load($customer_id);
        $store =  $customer->getStore();
        $dataVar = [
            "customer"=> $customer,
            "store"=>$store
        ];
        try {
            //send email to customer
            $this->getEmailHelper()->sendTransactionEmail(
                $types[$type],
                \Magento\Framework\App\Area::AREA_FRONTEND,
                self::XML_PATH_REGISTER_EMAIL_IDENTITY,
                $customer->getEmail(),
                $dataVar,
                '',
                $store->getId()
            );

            $adminEmailto = $this->_scopeConfig->getValue(self::XML_PATH_ADMIN_EMAIL_TO);
            if($adminEmailto){
                //send email to vendor
                $this->getEmailHelper()->sendTransactionEmail(
                    $adminTypes[$type],
                    \Magento\Framework\App\Area::AREA_FRONTEND,
                    self::XML_PATH_REGISTER_EMAIL_IDENTITY,
                    $adminEmailto,
                    $dataVar
                );
            }


        } catch (\Exception $e) {
        }
        return $this;
    }
}
