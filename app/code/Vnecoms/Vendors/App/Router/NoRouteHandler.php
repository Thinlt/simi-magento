<?php
/**
 * Backend no route handler
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\App\Router;

class NoRouteHandler implements \Magento\Framework\App\Router\NoRouteHandlerInterface
{
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Route\ConfigInterface
     */
    protected $routeConfig;

    /**
     * @param \Vnecoms\Vendors\Helper\Data $helper
     * @param \Magento\Framework\App\Route\ConfigInterface $routeConfig
     */
    public function __construct(
        \Vnecoms\Vendors\Helper\Data $helper,
        \Magento\Framework\App\Route\ConfigInterface $routeConfig
    ) {
        $this->helper = $helper;
        $this->routeConfig = $routeConfig;
    }

    /**
     * Check and process no route request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function process(\Magento\Framework\App\RequestInterface $request)
    {
        $requestPathParams = explode('/', trim($request->getPathInfo(), '/'));

        $areaFrontName = array_shift($requestPathParams);

        if ($areaFrontName === $this->helper->getAreaFrontName(true)) {
            $moduleName = $this->routeConfig->getRouteFrontName('account');
            $actionNamespace = 'noroute';
            $actionName = 'index';
            $request->setModuleName($moduleName)->setControllerName($actionNamespace)->setActionName($actionName);
            return true;
        }
        return false;
    }
}
