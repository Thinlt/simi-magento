<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\App;

/**
 * Generic backend controller
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    /**
     * Name of "is URLs checked" flag
     */
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    const XML_PATH_VENDOR_DESIGN_HEAD_DEFAULT_TITLE = 'vendors/design/head_default_title';
    
    /**
     * Session namespace to refer in other places
     */

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = [];


    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorsession;
    
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * @var \Vnecoms\Vendors\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var unknown
     */
    protected $_frontendUrl;
    
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var bool
     */
    protected $_canUseBaseUrl;
    
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = '';

    /**
     * @param \Vnecoms\Vendors\App\Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
        $this->_authorization = $context->getAuthorization();
        $this->_auth = $context->getAuth();
        $this->_helper = $context->getHelper();
        $this->_backendUrl = $context->getBackendUrl();
        $this->_frontendUrl = $context->getFrontendUrl();
        $this->_localeResolver = $context->getLocaleResolver();
        $this->_canUseBaseUrl = $context->getCanUseBaseUrl();
        $this->_vendorsession = $context->getSession();
        $this->_session = $context->getSession();
    }

    
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        $permission = new \Vnecoms\Vendors\Model\AclResult();
        $this->_eventManager->dispatch(
            'ves_vendor_check_acl',
            [
                'resource' => $this->_aclResource,
                'permission' => $permission
            ]
        );
        return $permission->isAllowed();
    }

    /**
     * Retrieve vendors session model object
     *
     * @return \Vnecoms\Vendors\Model\Session
     */
    protected function _getSession()
    {
        return $this->_vendorsession;
    }

    /**
     * @return \Magento\Framework\Message\ManagerInterface
     */
    protected function getMessageManager()
    {
        return $this->messageManager;
    }

    /**
     * Define active menu item in menu block
     *
     * @param string $itemId current active menu item
     * @return $this
     */
    protected function _setActiveMenu($itemId)
    {
        /** @var $menuBlock \Magento\Backend\Block\Menu */
        $menuBlock = $this->_view->getLayout()->getBlock('menu');
        if ($menuBlock) {
            $menuBlock->setActive($itemId);
            $parents = $menuBlock->getMenuModel()->getParentItems($itemId);
            foreach ($parents as $item) {
                /** @var $item \Magento\Backend\Model\Menu\Item */
                $this->_view->getPage()->getConfig()->getTitle()->prepend($item->getTitle());
            }
        }
        return $this;
    }

    /**
     * Define active menu item in menu block
     *
     * @param string $itemId current active menu item
     * @return $this
     */
    public function setActiveMenu($itemId)
    {
        return $this->_setActiveMenu($itemId);
    }
    
    /**
     * @param string $label
     * @param string $title
     * @param string|null $link
     * @return $this
     */
    protected function _addBreadcrumb($label, $title, $link = null)
    {
        $this->_view->getLayout()->getBlock('breadcrumbs')->addLink($label, $title, $link);
        return $this;
    }

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return $this
     */
    protected function _addContent(\Magento\Framework\View\Element\AbstractBlock $block)
    {
        return $this->_moveBlockToContainer($block, 'content');
    }

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return $this
     */
    protected function _addLeft(\Magento\Framework\View\Element\AbstractBlock $block)
    {
        return $this->_moveBlockToContainer($block, 'left');
    }

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return $this
     */
    protected function _addJs(\Magento\Framework\View\Element\AbstractBlock $block)
    {
        return $this->_moveBlockToContainer($block, 'js');
    }

    /**
     * Set specified block as an anonymous child to specified container
     *
     * The block will be moved to the container from previous parent after all other elements
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @param string $containerName
     * @return $this
     */
    private function _moveBlockToContainer(\Magento\Framework\View\Element\AbstractBlock $block, $containerName)
    {
        $this->_view->getLayout()->setChild($containerName, $block->getNameInLayout(), '');
        return $this;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->_helper->moduleEnabled()) {
            return $this->_redirectUrl($this->getFrontendUrl('no-route'));
        }
        
        if (!$this->_getSession()->isLoggedIn()) {
            $loginUrl = $this->_helper->getSellerRegisterType() == \Vnecoms\Vendors\Model\Source\RegisterType::TYPE_SEPARATED?
                'marketplace/seller/login':
                'customer/account/login';
            
            $redirectUrl = $this->_helper->isUsedCustomVendorUrl()?$this->getUrl('account/login'):$this->getFrontendUrl($loginUrl);
            
            if ($this->getRequest()->getParam('isAjax')) {
                $body = [
                    'ajaxExpired'   => 1,
                    'ajaxRedirect'  => $redirectUrl,
                ];
                $this->_getSession()->setBeforeAuthUrl($this->getUrl('dashboard'));
                return $this->_response->setBody(json_encode($body));
            } else {
                $this->_getSession()->setBeforeAuthUrl($this->_backendUrl->getCurrentUrl());
                $this->_redirectUrl($redirectUrl);
                $this->_actionFlag->set('', 'no-dispatch', true);
                return parent::dispatch($request);
            }
        }
        
        $vendor = $this->_getSession()->getVendor();
        if (!$vendor->getId()) {
            $this->messageManager->addError(__("Your account is not associated to any vendor account"));
            $this->_redirectUrl($this->getFrontendUrl('customer/account'));
            $this->_actionFlag->set('', 'no-dispatch', true);
            return parent::dispatch($request);
        }

        if ($vendor->getStatus() != \Vnecoms\Vendors\Model\Vendor::STATUS_APPROVED) {
            if ($vendor->getStatus() == \Vnecoms\Vendors\Model\Vendor::STATUS_DISABLED) {
                $this->messageManager->addError(__("Your seller account status is %1, You are not allowed to access this page", $vendor->getStatusLabel()));
                $this->_redirectUrl($this->getFrontendUrl('customer/account'));
                $this->_actionFlag->set('', 'no-dispatch', true);
                return parent::dispatch($request);
            }
            
            if (!in_array($this->getRequest()->getModuleName(), $this->_helper->getOpenModules())) {
                $this->messageManager->addError(__("Your seller account status is %1, You can not access to this function", $vendor->getStatusLabel()));
                $this->_redirectUrl($this->getUrl('dashboard'));
                $this->_actionFlag->set('', 'no-dispatch', true);
                return parent::dispatch($request);
            }
        }
        
        if (!$this->_isAllowed()) {
            $this->_response->setStatusHeader(403, '1.1', 'Forbidden');
            $this->_view->loadLayout(['default', 'vendors_denied'], true, true, false);
            $this->_view->renderLayout();
            $this->_request->setDispatched(true);
            return $this->_response;
        }
        
        return parent::dispatch($request);
    }

    /**
     * Set session locale,
     * process force locale set through url params
     *
     * @return $this
     */
    protected function _processLocaleSettings()
    {
        $forceLocale = $this->getRequest()->getParam('locale', null);
        if ($this->_objectManager->get('Magento\Framework\Validator\Locale')->isValid($forceLocale)) {
            $this->_getSession()->setSessionLocale($forceLocale);
        }

        if ($this->_getSession()->getLocale() === null) {
            $this->_getSession()->setLocale($this->_localeResolver->getLocale());
        }

        return $this;
    }

    /**
     * Set redirect into response
     *
     * @TODO MAGETWO-28356: Refactor controller actions to new ResultInterface
     * @param   string $path
     * @param   array $arguments
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirect($path, $arguments = [])
    {
        $this->_getSession()->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        $this->getResponse()->setRedirect($this->getUrl($path, $arguments));
        return $this->getResponse();
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->_getSession()->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        $this->getResponse()->setRedirect($url);
        return $this->getResponse();
    }
    /**
     * Forward to action
     *
     * @TODO MAGETWO-28356: Refactor controller actions to new ResultInterface
     * @param string $action
     * @param string|null $controller
     * @param string|null $module
     * @param array|null $params
     * @return void
     */
    protected function _forward($action, $controller = null, $module = null, array $params = null)
    {
        $this->_getSession()->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return parent::_forward($action, $controller, $module, $params);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->_helper->getUrl($route, $params);
    }
    
    /**
     * Get Frontend Url
     * @param string $route
     * @param array $params
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }
}
