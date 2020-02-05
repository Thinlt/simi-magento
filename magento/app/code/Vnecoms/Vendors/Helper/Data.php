<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Data extends AbstractHelper
{
    const XML_PATH_EXTENSION_ENABLE = 'vendors/general/enabled';
    const XML_PATH_VENDOR_PANEL_TYPE = 'vendors/design/panel_type';
    const XML_PATH_USE_CUSTOM_VENDOR_URL = 'vendors/url/use_custom';
    
    const XML_PATH_VENDOR_FOOTER_TEXT = 'vendors/design/footer_text';
    
    const XML_PATH_ENABLE_VENDOR_REGISTER       = 'vendors/create_account/vendor_register';
    const XML_PATH_ENABLE_VENDOR_REGISTER_TYPE  = 'vendors/create_account/vendor_register_type';
    const XML_PATH_ENABLE_VENDOR_REGISTER_BLOCK = 'vendors/create_account/vendor_register_static_block';
    const XML_PATH_ENABLE_VENDOR_APPROVAL       = 'vendors/create_account/vendor_approval';
    const XML_PATH_VENDOR_DEFAULT_GROUP         = 'vendors/create_account/default_group';
    const XML_PATH_ENABLE_AGREEMENT             = 'vendors/create_account/enable_agreement';
    const XML_PATH_AGREEMENT_LABEL              = 'vendors/create_account/agreement_label';
    
    const XML_PATH_SHOW_VENDOR_DESCRIPTION = 'vendors/profile/show_short_description';
    const XML_PATH_SHOW_VENDOR_PHONE = 'vendors/profile/show_phone';
    const XML_PATH_SHOW_VENDOR_OPERATION_TIME = 'vendors/profile/show_operation_time';
    const XML_PATH_ADDRESS_TEMPLATE = 'vendors/profile/address_template';
    const XML_PATH_DESCRIPTION_MAX_LENGTH = 'vendors/profile/description_size';
    const PROFILE_FORM          = 'profile_form';
    const ADMIN_PROFILE_FORM    = 'admin_profile_form';
    const REGISTRATION_FORM     = 'registration_form';
    
    
    /**
     * @var string
     */
    protected $_pageHelpUrl;

    /**
     * @var \Magento\Framework\App\Route\Config
     */
    protected $_routeConfig;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_locale;

    /**
     * @var \Vnecoms\Vendors\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * @var \Vnecoms\Vendors\App\Area\FrontNameResolver
     */
    protected $_frontNameResolver;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * These attributes will not be saved from vendor cpanel.
     * @var array
     */
    protected $_notSaveVendorAttributes;
    
    /**
     * Modules that will use template files from adminhtml area
     * @var array
     */
    protected $_modulesUseTemplateFromAdminhtml;
    
    /**
     * Blocks that will use template files from adminhtml area
     * @var array
     */
    
    protected $_blocksUseTemplateFromAdminhtml;
    
    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_configHelper;
    
    /**
     * Seller can still access to these modules if his status is not approved
     * 
     * @var array
     */
    protected $_openModules;
    
    /**
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Route\Config $routeConfig
     * @param \Magento\Framework\Locale\ResolverInterface $locale
     * @param \Vnecoms\Vendors\Model\UrlInterface $backendUrl
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Vnecoms\Vendors\App\Area\FrontNameResolver $frontNameResolver
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Vnecoms\VendorsConfig\Helper\Data $configHelper
     * @param array $notSaveVendorAttribute
     * @param array $modulesUseTemplateFromAdminhtml
     * @param array $blocksUseTemplateFromAdminhtml
     * @param array $openModules
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Route\Config $routeConfig,
        \Magento\Framework\Locale\ResolverInterface $locale,
        \Vnecoms\Vendors\Model\UrlInterface $backendUrl,
        \Magento\Backend\Model\Auth $auth,
        \Vnecoms\Vendors\App\Area\FrontNameResolver $frontNameResolver,
        \Magento\Framework\Math\Random $mathRandom,
        \Vnecoms\VendorsConfig\Helper\Data $configHelper,
        array $notSaveVendorAttribute=[],
        array $modulesUseTemplateFromAdminhtml=[],
        array $blocksUseTemplateFromAdminhtml =[],
        array $openModules = []
    ) {
        parent::__construct($context);
        $this->_configHelper = $configHelper;
        $this->_routeConfig = $routeConfig;
        $this->_locale = $locale;
        $this->_backendUrl = $backendUrl;
        $this->_auth = $auth;
        $this->_frontNameResolver = $frontNameResolver;
        $this->mathRandom = $mathRandom;
        $this->_notSaveVendorAttributes = $notSaveVendorAttribute;
        $this->_modulesUseTemplateFromAdminhtml = $modulesUseTemplateFromAdminhtml;
        $this->_blocksUseTemplateFromAdminhtml = $blocksUseTemplateFromAdminhtml;
        $this->_openModules = $openModules;
    }

    /**
     * Is module enabled
     * 
     * @return boolean
     */
    public function moduleEnabled(){
        return $this->scopeConfig->getValue(self::XML_PATH_EXTENSION_ENABLE);
    }
    
    /**
     * Get vendor panel type
     *
     * @return int
     */
    public function getPanelType(){
        return $this->scopeConfig->getValue(self::XML_PATH_VENDOR_PANEL_TYPE);
    }
    
    /**
     * Get vendor panel type
     *
     * @return int
     */
    public function getSellerRegisterType(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE_VENDOR_REGISTER_TYPE,$storeScope);
    }
    
    /**
     * Get vendor register static block ID
     *
     * @return int
     */
    public function getSellerRegisterStaticBlock(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE_VENDOR_REGISTER_BLOCK,$storeScope);
    }
    
    /**
     * Is enabled registration agreement.
     *
     * @return int
     */
    public function isEnabledRegistrationAgreement(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE_AGREEMENT,$storeScope);
    }
    
    /**
     * Ger Agreement Label
     * 
     * @return \Magento\Framework\App\Config\mixed
     */
    public function getAgreementLabel(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_AGREEMENT_LABEL,$storeScope);
    }
    
    /**
     * Get footer text
     * 
     * @return string
     */
    public function getFooterText(){
        return $this->scopeConfig->getValue(self::XML_PATH_VENDOR_FOOTER_TEXT);
    }
    
    /**
     * @return string
     */
    public function getPageHelpUrl()
    {
        if (!$this->_pageHelpUrl) {
            $this->setPageHelpUrl();
        }
        return $this->_pageHelpUrl;
    }

    /**
     * @param string|null $url
     * @return $this
     */
    public function setPageHelpUrl($url = null)
    {
        if ($url === null) {
            $request = $this->_request;
            $frontModule = $request->getControllerModule();
            if (!$frontModule) {
                $frontModule = $this->_routeConfig->getModulesByFrontName($request->getModuleName());
                if (empty($frontModule) === false) {
                    $frontModule = $frontModule[0];
                } else {
                    $frontModule = null;
                }
            }
            $url = 'http://www.magentocommerce.com/gethelp/';
            $url .= $this->_locale->getLocale() . '/';
            $url .= $frontModule . '/';
            $url .= $request->getControllerName() . '/';
            $url .= $request->getActionName() . '/';

            $this->_pageHelpUrl = $url;
        }
        $this->_pageHelpUrl = $url;

        return $this;
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function addPageHelpUrl($suffix)
    {
        $this->_pageHelpUrl = $this->getPageHelpUrl() . $suffix;
        return $this;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->_backendUrl->getUrl($route, $params);
    }

    /**
     * @return int|bool
     */
    public function getCurrentUserId()
    {
        if ($this->_auth->getUser()) {
            return $this->_auth->getUser()->getId();
        }
        return false;
    }

    /**
     * Decode filter string
     *
     * @param string $filterString
     * @return array
     */
    public function prepareFilterString($filterString)
    {
        $data = [];
        $filterString = base64_decode($filterString);
        parse_str($filterString, $data);
        array_walk_recursive(
            $data,
            // @codingStandardsIgnoreStart
            /**
             * Decodes URL-encoded string and trims whitespaces from the beginning and end of a string
             *
             * @param string $value
             */
            // @codingStandardsIgnoreEnd
            function (&$value) {
                $value = trim(rawurldecode($value));
            }
        );
        return $data;
    }

    /**
     * Generate unique token for reset password confirmation link
     *
     * @return string
     */
    public function generateResetPasswordLinkToken()
    {
        return $this->mathRandom->getUniqueHash();
    }

    /**
     * Get backend start page URL
     *
     * @return string
     */
    public function getHomePageUrl()
    {
        return $this->_backendUrl->getRouteUrl('vendors');
    }

    /**
     * Return Backend area front name
     *
     * @param bool $checkHost
     * @return bool|string
     */
    public function getAreaFrontName($checkHost = false)
    {
        return $this->_frontNameResolver->getFrontName($checkHost);
    }
    
    /**
     * Get the list of attributes which will not be saved from vendor cpanel.
     * @return array:
     */
    public function getNotSavedVendorAttributes(){
        return $this->_notSaveVendorAttributes;
    }
    
    /**
     * Get all modules that the extension will use the tempalte from adminhtml area instead of vendors area
     * @return array:
     */
    public function getModulesUseTemplateFromAdminhtml(){
        return $this->_modulesUseTemplateFromAdminhtml;
    }
    
		/**
     * Get all block classes that the extension will use the tempalte from adminhtml area instead of vendors area
		 * @return array
     */
    public function getBlocksUseTemplateFromAdminhtml(){
				$result = [];
				foreach($this->_blocksUseTemplateFromAdminhtml as $class){
					$result[] = $class;
					$result[] = $class.'\Interceptor';
				}
        return $result;
    }
    
    /**
     * Get open modules
     * 
     * @return multitype:
     */
    public function getOpenModules(){
        return $this->_openModules;
    }
    
    /**
     * Is enabled vendor register
     * @return boolean
     */
    public function isEnableVendorRegister(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE_VENDOR_REGISTER,$storeScope);
    }
    
    /**
     * Is required vendor approval
     * @return boolean
     */
    public function isRequiredVendorApproval(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE_VENDOR_APPROVAL,$storeScope);
    }
    
    /**
     * Get default vendor group ID
     * @return int
     */
    public function getDefaultVendorGroup(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_VENDOR_DEFAULT_GROUP,$storeScope);
    }
    
    /**
     * Show vendor short description in profile block
     * 
     * @return boolean
     */
    public function showVendorShortDescription(){
        return $this->scopeConfig->getValue(self::XML_PATH_SHOW_VENDOR_DESCRIPTION);
    }
    
    /**
     * Get address max length
     * @return int
     */
    public function getDescriptionMaxLength(){
        return $this->scopeConfig->getValue(self::XML_PATH_DESCRIPTION_MAX_LENGTH);
    }
    
    /**
     * Can show vendor phone number on profile block
     * @return int
     */
    public function showVendorPhoneNumber(){
        return $this->scopeConfig->getValue(self::XML_PATH_SHOW_VENDOR_PHONE);
    }
    
    /**
     * Can show vendor's store operation time on profile block
     * @return int
     */
    public function showVendorOperationTime(){
        return $this->scopeConfig->getValue(self::XML_PATH_SHOW_VENDOR_OPERATION_TIME);
    }
    
    /**
     * Get address template of profile block
     * @return int
     */
    public function getAddressTemplate(){
        return $this->scopeConfig->getValue(self::XML_PATH_ADDRESS_TEMPLATE);
    }

    /**
     * Get store Name
     *
     * @param int $vendorId
     */
    public function getVendorStoreName($vendorId){
        return $this->_configHelper->getVendorConfig('general/store_information/name', $vendorId);
    }
    
    /**
     * Get store short description
     *
     * @param int $vendorId
     */
    public function getVendorStoreShortDescription($vendorId){
        return $this->_configHelper->getVendorConfig('general/store_information/short_description', $vendorId);
    }
    
    /**
     * Get store short description
     *
     * @param int $vendorId
     */
    public function getVendorPhone($vendorId){
        return $this->_configHelper->getVendorConfig('general/store_information/phone', $vendorId);
    }
    
    /**
     * Get store short description
     *
     * @param int $vendorId
     */
    public function getVendorOperationTime($vendorId){
        return $this->_configHelper->getVendorConfig('general/store_information/hours', $vendorId);
    }
    
    /**
     * Get Not active vendor ids
     * 
     * @return array
     */
    public function getNotActiveVendorIds(){
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $om->create('Vnecoms\Vendors\Model\ResourceModel\Vendor\Collection');
        $collection->addAttributeToFilter('status',['neq' => \Vnecoms\Vendors\Model\Vendor::STATUS_APPROVED]);
        return $collection->getAllIds();
    }
    
    /**
     * Is used custom vendor url
     * 
     * @return boolean
     */
    public function isUsedCustomVendorUrl(){
        return $this->scopeConfig->getValue(\Vnecoms\Vendors\App\Area\FrontNameResolver::XML_PATH_USE_CUSTOM_VENDOR_URL);
    }
    
}

