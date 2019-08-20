<?php

namespace Vnecoms\Vendors\Model;

class Feed extends \Magento\AdminNotification\Model\Feed
{
    
    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;
    
    /**
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\ConfigInterface $backendConfig
     * @param \Magento\AdminNotification\Model\InboxFactory $inboxFactory
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\ConfigInterface $backendConfig,
        \Magento\AdminNotification\Model\InboxFactory $inboxFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $backendConfig,
            $inboxFactory,
            $curlFactory,
            $deploymentConfig,
            $productMetadata,
            $urlBuilder,
            $resource,
            $resourceCollection
        );
        $this->_moduleList = $moduleList;
    }


    /**
     * Init model
     *
     * @return void
     */
    protected function _construct()
    {
    }

    /**
     * @return string
     */
    public function getFeedUrl()
    {
        $httpPath = $this->_backendConfig->isSetFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://';
        if ($this->_feedUrl === null) {
            $modules = $this->_moduleList->getAll();
            $vnecomsExt = [];
            foreach ($modules as $moduleName => $moduleInfo) {
                $nameSpace  = explode('_', $moduleName);
                if ((sizeof($nameSpace) == 2) && ($nameSpace[0] == 'Vnecoms')) {
                    $vnecomsExt[] = $moduleName.'|'.(isset($moduleInfo['setup_version'])?$moduleInfo['setup_version']:'');
                }
            }
            
            $params = [];
            $params[] = 'exts='.urlencode(implode('||', $vnecomsExt));
            $params[] = 'url='.urlencode($this->_backendConfig->getValue('web/unsecure/base_url'));
            $params = implode('&', $params);
            $this->_feedUrl = $httpPath . 'www.vnecoms.com/news/rss/?'.$params;
        }
        return $this->_feedUrl;
    }

    /**
     * Retrieve Update Frequency
     *
     * @return int
     */
    public function getFrequency()
    {
        return 172800; /*2 days*/
    }

    /**
     * Retrieve Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return $this->_cacheManager->load('vnecoms_admin_version_lastcheck');
    }

    /**
     * Set last update time (now)
     *
     * @return $this
     */
    public function setLastUpdate()
    {
        $this->_cacheManager->save(time(), 'vnecoms_admin_version_lastcheck');
        return $this;
    }
}
