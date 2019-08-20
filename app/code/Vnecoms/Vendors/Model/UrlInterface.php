<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model;

interface UrlInterface extends \Magento\Framework\UrlInterface
{
    /**
     * xpath to startup page in configuration
     */
    const XML_PATH_VENDOR_STARTUP_MENU_ITEM = 'vendors/general/start_page';
    
    
    const XML_PATH_USE_CUSTOM_VENDOR_PATH = 'vendors/url/use_custom_path';
    
    const XML_PATH_CUSTOM_VENDOR_PATH = 'vendors/url/custom_path';

    /**
     * Generate secret key for controller and action based on form key
     *
     * @param string $routeName
     * @param string $controller Controller name
     * @param string $action Action name
     * @return string
     */
    public function getSecretKey($routeName = null, $controller = null, $action = null);

    /**
     * Return secret key settings flag
     *
     * @return bool
     */
    public function useSecretKey();

    /**
     * Enable secret key using
     *
     * @return \Magento\Backend\Model\UrlInterface
     */
    public function turnOnSecretKey();

    /**
     * Disable secret key using
     *
     * @return \Magento\Backend\Model\UrlInterface
     */
    public function turnOffSecretKey();

    /**
     * Refresh admin menu cache etc.
     *
     * @return \Magento\Backend\Model\UrlInterface
     */
    public function renewSecretUrls();

    /**
     * Find admin start page url
     *
     * @return string
     */
    public function getStartupPageUrl();

    /**
     * Set custom auth session
     *
     * @param \Magento\Backend\Model\Auth\Session $session
     * @return \Vnecoms\Vendors\Model\UrlInterface
     */
    public function setSession(\Magento\Backend\Model\Auth\Session $session);

    /**
     * Return backend area front name, defined in configuration
     *
     * @return string
     */
    public function getAreaFrontName();

    /**
     * Find first menu item that user is able to access
     *
     * @return string
     */
    public function findFirstAvailableMenu();
}
