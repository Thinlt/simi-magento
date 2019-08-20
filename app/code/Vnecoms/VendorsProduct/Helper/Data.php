<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use \Vnecoms\VendorsProduct\Model\Source\Approval;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Data extends AbstractHelper
{
    const XML_CATALOG_ALLOW_VENDOR_SET_WEBSITE  = 'vendors/catalog/can_set_website';
    const XML_CATALOG_NEW_PRODUCT_APPROVAL      = 'vendors/catalog/new_product_approval';
    const XML_CATALOG_UPDATE_PRODUCT_APPROVAL   = 'vendors/catalog/update_product_approval';
    const XML_CATALOG_UPDATE_ATTRIBUTE_APPROVAL_FLAG   = 'vendors/catalog/attribute_approval_flag';
    const XML_CATALOG_UPDATE_ATTRIBUTE_APPROVAL   = 'vendors/catalog/attribute_approval';
    const XML_CATALOG_EMAIL_SENDER              = 'vendors/catalog/sender_email_identity';
    const XML_CATALOG_ADMIN_EMAIL               = 'vendors/catalog/admin_email_identity';
    const XML_CATALOG_PRODUCT_TYPE_RESTRICTION  = 'vendors/catalog/product_type_restriction';
    const XML_CATALOG_ATTRIBUTE_SET_RESTRICTION = 'vendors/catalog/attribute_set_restriction';
    const XML_CATALOG_ATTRIBUTE_RESTRICTION     = 'vendors/catalog/attribute_restriction';
    const XML_CATALOG_NEW_PRODUCT_APPROVAL_EMAIL_ADMIN      = 'vendors/catalog/new_product_approval_email_admin';
    const XML_CATALOG_UPDATE_PRODUCT_APPROVAL_EMAIL_ADMIN   = 'vendors/catalog/update_product_approval_email_admin';
    const XML_CATALOG_PRODUCT_APPROVED_EMAIL_VENDOR         = 'vendors/catalog/product_approved_email_vendor';
    const XML_CATALOG_UPDATE_PRODUCT_APPROVED_EMAIL_VENDOR  = 'vendors/catalog/update_product_approved_email_vendor';
    const XML_CATALOG_PRODUCT_DENIED_EMAIL_VENDOR           = 'vendors/catalog/product_denied_email_vendor';
    const XML_CATALOG_UPDATE_PRODUCT_DENIED_EMAIL_VENDOR    = 'vendors/catalog/update_product_denied_email_vendor';
    
    
    /**
     * These attributes will not be saved from vendor cpanel.
     * @var array
     */
    protected $_notAllowedProductAttributes;
    
    /**
     * @var \Vnecoms\Vendors\Helper\Email
     */
    protected $_emailHelper;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * These attributes will not be saved from vendor cpanel.
     * @var array
     */
    protected $_joinProductAttribute;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Route\Config $routeConfig
     * @param \Magento\Framework\Locale\ResolverInterface $locale
     * @param \Vnecoms\Vendors\Model\UrlInterface $backendUrl
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Vnecoms\Vendors\App\Area\FrontNameResolver $frontNameResolver
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param array $notSaveVendorAttribute
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Vnecoms\Vendors\Helper\Email $emailHelper,
        array $notAllowedProductAttributes = [],
        array $joinProductAttribute = []
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->_notAllowedProductAttributes = $notAllowedProductAttributes;
        $this->_joinProductAttribute = $joinProductAttribute;
        $this->_emailHelper = $emailHelper;
    }


    /**
     * Get the list of product attributes which will not be used on vendor cpanel.
     * @return array
     */
    public function getJoinProductAttribute()
    {
        return $this->_joinProductAttribute;
    }

    
    /**
     * Get the list of product attributes which will not be used on vendor cpanel.
     * @return array
     */
    public function getNotUsedVendorAttributes()
    {
        $attributeRestriction = $this->scopeConfig->getValue(self::XML_CATALOG_ATTRIBUTE_RESTRICTION);
        $attributeRestriction = $attributeRestriction?explode(',', $attributeRestriction):[];
        
        return array_merge($this->_notAllowedProductAttributes, $attributeRestriction);
    }
    
    /**
     * Can vendor set website id for product
     * 
     * @return boolean
     */
    public function canVendorSetWebsite(){
        return (bool)$this->scopeConfig->getValue(self::XML_CATALOG_ALLOW_VENDOR_SET_WEBSITE);
    }
    
    /**
     * Is Required approval for new products
     * @return boolean
     */
    public function isNewProductsApproval()
    {
        return $this->scopeConfig->getValue(self::XML_CATALOG_NEW_PRODUCT_APPROVAL);
    }
    
    /**
     * Is Required approval for updating product info
     * @return boolean
     */
    public function isUpdateProductsApproval()
    {
        return $this->scopeConfig->getValue(self::XML_CATALOG_UPDATE_PRODUCT_APPROVAL);
    }


    /**
     * Is Required approval for updating product info
     * @return boolean
     */
    public function getUpdateProductsApprovalFlag()
    {
        return $this->scopeConfig->getValue(self::XML_CATALOG_UPDATE_ATTRIBUTE_APPROVAL_FLAG);
    }

    /**
     * Is Required approval for updating product info
     * @return array|null
     */
    public function getUpdateProductsApprovalAttributes()
    {
        return explode(",",$this->scopeConfig->getValue(self::XML_CATALOG_UPDATE_ATTRIBUTE_APPROVAL));
    }
    
    /**
     * If the product have these approval status, it will be displayed in frontend.
     * @return array
     */
    public function getAllowedApprovalStatus()
    {
        return [
            Approval::STATUS_APPROVED,
            Approval::STATUS_PENDING_UPDATE,
        ];
    }
    /**
     * Send new product approval notification email to admin.
     * @param \Magento\Catalog\Model\Product $product
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     */
    public function sendNewProductApprovalEmailToAdmin(
        \Magento\Catalog\Model\Product $product,
        \Vnecoms\Vendors\Model\Vendor $vendor
    ) {
        $adminEmail = $this->scopeConfig->getValue(self::XML_CATALOG_ADMIN_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminEmail) {
            return;
        }
        $adminEmail = str_replace(" ", "", $adminEmail);
        $adminEmail = explode(",", $adminEmail);
        try {
            $this->_emailHelper->sendTransactionEmail(
                self::XML_CATALOG_NEW_PRODUCT_APPROVAL_EMAIL_ADMIN,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                self::XML_CATALOG_EMAIL_SENDER,
                $adminEmail,
                ['product'=>$product, 'vendor'=>$vendor]
            );
        } catch (\Exception $e) {
        }
    }
    
    /**
     * Send Update product approval notification email to admin
     * @param \Magento\Catalog\Model\Product $product
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     */
    public function sendUpdateProductApprovalEmailToAdmin(
        \Magento\Catalog\Model\Product $product,
        \Vnecoms\Vendors\Model\Vendor $vendor
    ) {
        $adminEmail = $this->scopeConfig->getValue(self::XML_CATALOG_ADMIN_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminEmail) {
            return;
        }
        $adminEmail = str_replace(" ", "", $adminEmail);
        $adminEmail = explode(",", $adminEmail);

        try {
            $this->_emailHelper->sendTransactionEmail(
                self::XML_CATALOG_UPDATE_PRODUCT_APPROVAL_EMAIL_ADMIN,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                self::XML_CATALOG_EMAIL_SENDER,
                $adminEmail,
                ['product'=>$product, 'vendor'=>$vendor]
            );
        } catch (\Exception $e) {
        }
    }
    
    /**
     * Send new product approved notification email to vendor
     * @param \Magento\Catalog\Model\Product $product
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     */
    public function sendProductApprovedEmailToVendor(
        \Magento\Catalog\Model\Product $product,
        \Vnecoms\Vendors\Model\Vendor $vendor
    ) {
        try {
            $this->_emailHelper->sendTransactionEmail(
                self::XML_CATALOG_PRODUCT_APPROVED_EMAIL_VENDOR,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                self::XML_CATALOG_EMAIL_SENDER,
                $vendor->getCustomer()->getEmail(),
                ['product' => $product, 'vendor' => $vendor]
            );
        } catch (\Exception $e) {
        }
    }
    
    /**
     * Send update product approved notification email to vendor
     * @param \Magento\Catalog\Model\Product $product
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     */
    public function sendUpdateProductApprovedEmailToVendor(
        \Magento\Catalog\Model\Product $product,
        \Vnecoms\Vendors\Model\Vendor $vendor,
        \Vnecoms\VendorsProduct\Model\ResourceModel\Product\Update\Collection $updateCollection
    ) {
        try {
            $this->_emailHelper->sendTransactionEmail(
                self::XML_CATALOG_UPDATE_PRODUCT_APPROVED_EMAIL_VENDOR,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                self::XML_CATALOG_EMAIL_SENDER,
                $vendor->getCustomer()->getEmail(),
                ['product'=>$product, 'vendor'=>$vendor, 'updates' => $updateCollection]
            );
        } catch (\Exception $e) {
        }
    }
    
    /**
     * Send new product unapproved notification email to vendor
     * @param \Magento\Catalog\Model\Product $product
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     */
    public function sendProductUnapprovedEmailToVendor(
        \Magento\Catalog\Model\Product $product,
        \Vnecoms\Vendors\Model\Vendor $vendor
    ) {
        try {
            $this->_emailHelper->sendTransactionEmail(
                self::XML_CATALOG_PRODUCT_DENIED_EMAIL_VENDOR,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                self::XML_CATALOG_EMAIL_SENDER,
                $vendor->getCustomer()->getEmail(),
                ['product' => $product, 'vendor' => $vendor]
            );
        } catch (\Exception $e) {
        }
    }
    
    /**
     * Send update product unapproved notification email to vendor
     * @param \Magento\Catalog\Model\Product $product
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     */
    public function sendUpdateProductUnapprovedEmailToVendor(
        \Magento\Catalog\Model\Product $product,
        \Vnecoms\Vendors\Model\Vendor $vendor,
        \Vnecoms\VendorsProduct\Model\ResourceModel\Product\Update\Collection $updateCollection
    ) {
        try {
            $this->_emailHelper->sendTransactionEmail(
                self::XML_CATALOG_UPDATE_PRODUCT_DENIED_EMAIL_VENDOR,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                self::XML_CATALOG_EMAIL_SENDER,
                $vendor->getCustomer()->getEmail(),
                ['product'=>$product, 'vendor'=>$vendor, 'updates' => $updateCollection]
            );
        } catch (\Exception $e) {
        }
    }
    
    /**
     * Get product type restriction
     * @return \Magento\Framework\App\Config\mixed
     */
    public function getProductTypeRestriction()
    {
        return explode(",", $this->scopeConfig->getValue(self::XML_CATALOG_PRODUCT_TYPE_RESTRICTION));
    }
    
    /**
     * Get attribute set restriction
     * @return \Magento\Framework\App\Config\mixed
     */
    public function getAttributeSetRestriction()
    {
        return explode(",", $this->scopeConfig->getValue(self::XML_CATALOG_ATTRIBUTE_SET_RESTRICTION));
    }
}
