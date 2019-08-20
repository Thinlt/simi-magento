<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Model\Order\Email\Container;

use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Container;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Vnecoms\VendorsConfig\Helper\Data;

class CreditmemoIdentity extends Container implements IdentityInterface
{
    const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/creditmemo/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'sales_email/creditmemo/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'vendors/sales/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE = 'vendors/sales/creditmemo_new_template';
    const XML_PATH_EMAIL_ENABLED = 'sales_email/creditmemo/enabled';

    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_vendorConfig;


    /**
     * @var string
     */
    protected $_vendorId;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Data $vendorConfig
    ) {
        $this->_vendorConfig = $vendorConfig;
        parent::__construct($scopeConfig, $storeManager);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_vendorConfig->getVendorConfig(self::XML_PATH_EMAIL_ENABLED, $this->getVendorId());
    }

    /**
     * Return email copy_to list
     *
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        $data = $this->_vendorConfig->getVendorConfig(self::XML_PATH_EMAIL_COPY_TO, $this->getVendorId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    /**
     * Set vendor Id
     *
     * @param string $vendorId
     * @return void
     */
    public function setVendorId($vendorId)
    {
        $this->_vendorId = $vendorId;
    }

    /**
     * Return vendor Id
     *
     * @return string
     */
    public function getVendorId()
    {
        return  $this->_vendorId ;
    }


    /**
     * Return copy method
     *
     * @return mixed
     */
    public function getCopyMethod()
    {
        return $this->_vendorConfig->getVendorConfig(self::XML_PATH_EMAIL_COPY_METHOD, $this->getVendorId());
    }

    /**
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getEmailIdentity()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }
}
