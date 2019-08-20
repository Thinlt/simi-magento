<?php
namespace Vnecoms\Vendors\Model;

use Magento\Store\Model\Store as StoreModel;

class VendorPathConfig extends \Magento\Backend\Model\AdminPathConfig
{
    /**
     * @var \Vnecoms\Vendors\App\ConfigInterface
     */
    protected $vendorConfig;

    const XML_PATH_SECURE_IN_VENDORS = 'web/secure/use_in_vendors';
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig
     * @param \Magento\Backend\App\ConfigInterface $backendConfig
     * @param \Magento\Framework\UrlInterface $url
     * @param \Vnecoms\Vendors\App\ConfigInterface $vendorsConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig,
        \Magento\Backend\App\ConfigInterface $backendConfig,
        \Magento\Framework\UrlInterface $url,
        \Vnecoms\Vendors\App\ConfigInterface $vendorsConfig
    ) {
        $this->vendorConfig = $vendorsConfig;
        parent::__construct($coreConfig, $backendConfig, $url);
    }
    
    /**
     * @return string
     */
    public function getDefaultPath()
    {
        return $this->backendConfig->getValue('web/default/vendors');
    }
    
    /**
     * {@inheritdoc}
     *
     * @param string $path
     * @return bool
     */
    public function shouldBeSecure($path)
    {
        return parse_url(
            (string)$this->coreConfig->getValue(StoreModel::XML_PATH_UNSECURE_BASE_URL, 'website'),
            PHP_URL_SCHEME
        ) === 'https'
        || $this->backendConfig->isSetFlag(self::XML_PATH_SECURE_IN_VENDORS)
        && parse_url(
            (string)$this->coreConfig->getValue(StoreModel::XML_PATH_SECURE_BASE_URL, 'website'),
            PHP_URL_SCHEME
        ) === 'https';
    }
}
