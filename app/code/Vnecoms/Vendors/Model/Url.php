<?php
namespace Vnecoms\Vendors\Model;

use Magento\Framework\App\ObjectManager;
class Url extends \Magento\Backend\Model\Url implements \Vnecoms\Vendors\Model\UrlInterface
{
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $backendHelper;

    /**
     * Menu config
     *
     * @var \Vnecoms\Vendors\Model\Menu\Config
     */
    protected $menuConfig;
    
    /**
     * 
     * @param \Magento\Framework\App\Route\ConfigInterface $routeConfig
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Url\SecurityInfoInterface $urlSecurityInfo
     * @param \Magento\Framework\Url\ScopeResolverInterface $scopeResolver
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Framework\Session\SidResolverInterface $sidResolver
     * @param \Magento\Framework\Url\RouteParamsResolverFactory $routeParamsResolverFactory
     * @param \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Url\RouteParamsPreprocessorInterface $routeParamsPreprocessor
     * @param string $scopeType
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Backend\Model\Menu\Config $menuConfig
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Vnecoms\Vendors\Helper\Data $vendorBackendHelper
     * @param \Vnecoms\Vendors\Model\Menu\Config $vendorMenuConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Route\ConfigInterface $routeConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Url\SecurityInfoInterface $urlSecurityInfo,
        \Magento\Framework\Url\ScopeResolverInterface $scopeResolver,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Url\RouteParamsResolverFactory $routeParamsResolverFactory,
        \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Url\RouteParamsPreprocessorInterface $routeParamsPreprocessor,
        $scopeType,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Backend\Model\Menu\Config $menuConfig,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Vnecoms\Vendors\Helper\Data $vendorBackendHelper,
        \Vnecoms\Vendors\Model\Menu\Config $vendorMenuConfig,
        array $data = []
    ) {
        $this->_encryptor = $encryptor;
        
        parent::__construct(
            $routeConfig,
            $request,
            $urlSecurityInfo,
            $scopeResolver,
            $session,
            $sidResolver,
            $routeParamsResolverFactory,
            $queryParamsResolver,
            $scopeConfig,
            $routeParamsPreprocessor,
            $scopeType,
            $backendHelper,
            $menuConfig,
            $cache,
            $authSession,
            $encryptor,
            $storeFactory,
            $formKey,
            $data
        );
        
        $this->backendHelper = $vendorBackendHelper;
        $this->menuConfig = $vendorMenuConfig;
    }

    /**
     * Return secret key settings flag
     *
     * @return bool
     */
    public function useSecretKey()
    {
        return false;
        //return $this->_scopeConfig->isSetFlag(self::XML_PATH_USE_SECURE_KEY) && !$this->getNoSecret();
    }

    /**
     * Find admin start page url
     *
     * @return string
     */
    public function getStartupPageUrl()
    {
        $menuItem = $this->_getMenu()->get(
            $this->_scopeConfig->getValue(self::XML_PATH_VENDOR_STARTUP_MENU_ITEM, $this->_scopeType)
        );
        if ($menuItem !== null) {
            if ($menuItem->isAllowed() && $menuItem->getAction()) {
                return $this->getUrl($menuItem->getAction());
            }
        }
        return $this->getUrl($this->findFirstAvailableMenu());
    }

    /**
     * Get Menu model
     *
     * @return \Magento\Backend\Model\Menu
     */
    protected function _getMenu()
    {
        if ($this->_menu === null) {
            $this->_menu = $this->menuConfig->getMenu();
        }
        return $this->_menu;
    }
    /**
     * Return backend area front name, defined in configuration
     *
     * @return string
     */
    public function getAreaFrontName()
    {
        $resolver = ObjectManager::getInstance()->get('Vnecoms\Vendors\App\Area\FrontNameResolver');
        return $resolver->getFrontName(true);
    }

//     /**
//      * Set Route Parameters
//      *
//      * @param string $data
//      * @return \Magento\Framework\UrlInterface
//      * @SuppressWarnings(PHPMD.CyclomaticComplexity)
//      */
//     protected function _setRoutePath($data)
//     {
//         if ($this->_getData('route_path') == $data) {
//             return $this;
//         }
    
//         $this->unsetData('route_path');
//         $routePieces = explode('/', $data);
    
// //         $route = array_shift($routePieces);
// //         if ('*' === $route) {
// //             $route = $this->_getRequest()->getRouteName();
// //         }
//         $this->_setRouteName('vendors');
    
//         $controller = '';
//         if (!empty($routePieces)) {
//             $controller = array_shift($routePieces);
//             if ('*' === $controller) {
//                 $controller = $this->_getRequest()->getControllerName();
//             }
//         }

//         $this->_setControllerName($controller);
    
//         $action = '';
//         if (!empty($routePieces)) {
//             $action = array_shift($routePieces);
//             if ('*' === $action) {
//                 $action = $this->_getRequest()->getActionName();
//             }
//         }
//         $this->_setActionName($action);
    
//         if (!empty($routePieces)) {
//             while (!empty($routePieces)) {
//                 $key = array_shift($routePieces);
//                 if (!empty($routePieces)) {
//                     $value = array_shift($routePieces);
//                     $this->getRouteParamsResolver()->setRouteParam($key, $value);
//                 }
//             }
//         }
    
//         return $this;
//     }
    
    /**
     * Retrieve action path.
     * Add backend area front name as a prefix to action path
     *
     * @return string
     */
    protected function _getActionPath()
    {
        if (!$this->_getRouteName()) {
            return '';
        }
    
        $hasParams = (bool) $this->_getRouteParams();
        $path = $this->_getRouteFrontName() . '/';

        if ($this->_getControllerName()) {
            $path .= $this->_getControllerName() . '/';
        } elseif ($hasParams) {
            $path .= self::DEFAULT_CONTROLLER_NAME . '/';
        }
        if ($this->_getActionName()) {
            $path .= $this->_getActionName() . '/';
        } elseif ($hasParams) {
            $path .= self::DEFAULT_ACTION_NAME . '/';
        }

        if ($path) {
            if ($this->getAreaFrontName()) {
                $path = $this->getAreaFrontName() . '/' . $path;
            }
        }
        return $path;
    }

    /**
     * Get scope for the url instance
     *
     * @return \Magento\Store\Model\Store
     */
    protected function _getScope()
    {
        if (!$this->hasData('scope')) {
            $this->setScope(null);
        }
        return $this->_getData('scope');
        /*
        if (!$this->_scope) {
            $this->_scope = $this->_storeFactory->create(
                [
                    'url' => $this,
                    'data' => ['force_disable_rewrites' => false, 'disable_store_in_url' => false],
                ]
            );
        }
        return $this->_scope; */
    }


    /**
     * Get cache id for config path
     *
     * @param string $path
     * @return string
     */
    protected function _getConfigCacheId($path)
    {
        return 'vendors/' . $path;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\Url::getBaseUrl()
     */
    public function getBaseUrl($params = []){
        if(!$this->_scopeConfig->getValue(\Vnecoms\Vendors\App\Area\FrontNameResolver::XML_PATH_USE_CUSTOM_VENDOR_URL)){
            return parent::getBaseUrl($params);
        }
        $customUrl = $this->_scopeConfig->getValue(\Vnecoms\Vendors\App\Area\FrontNameResolver::XML_PATH_CUSTOM_VENDOR_URL);
        $baseUrlInfo = parse_url($customUrl);
        $baseDomain = $baseUrlInfo['host'];
        
        $oldBaseUrl = parent::getBaseUrl($params);
        $oldBaseUrlInfo = parse_url($oldBaseUrl);
        $oldBaseDomain = $oldBaseUrlInfo['host'];
        
        return str_replace($oldBaseDomain, $baseDomain, $oldBaseUrl);
    }

}
