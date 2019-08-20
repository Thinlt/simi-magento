<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Core\Model;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Config\ConfigOptionsListConstants;

class Action implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_session;

    /**
     * @var array
     */
    protected $_extensionsList;

    /**
     * Application Cache Manager.
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cacheManager;

    /**
     * @var \Vnecoms\Core\Model\ResourceModel\Key\Collection
     */
    protected $_licenseCollection;

    public function __construct()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_actionFlag = $om->create('Magento\Framework\App\ActionFlag');
        $this->messageManager = $om->create('Magento\Framework\Message\ManagerInterface');
        $this->_session = $om->create('Magento\Backend\Model\Auth\Session');
        $this->_cacheManager = $om->create('\Magento\Framework\App\CacheInterface');
    }

    /**
     * Add free gift to shopping cart.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_session->isLoggedIn()) {
            return;
        }
        /* @var \Magento\Framework\App\Action\AbstractAction */
        $action = $observer->getControllerAction();
        /*Do with get request only*/
        if ($action->getRequest()->isGet()) {
            $moduleName = $action->getRequest()->getModuleName();
            $controllerModule = $action->getRequest()->getControllerModule();

            try {
                if (
                    $action instanceof \Magento\Backend\App\AbstractAction
                ) {
                    /* Check all installed vnecoms extension are activated or not*/
                    if (!is_array($this->_session->getData('vnecoms_check_license_data'))) {
                        $errors = $this->checkLicense();
                        $this->_session->setData('vnecoms_check_license_data', $errors);
                    } else {
                        $errors = $this->_session->getData('vnecoms_check_license_data');
                    }

                    $errorSize = sizeof($errors);
                    if (!$errorSize) {
                        return;
                    }

                    /*Show error message on all vnecoms page and dashboard*/
                    if (
                        (
                            !$this->_session->getData('vnecoms_core_notify') ||
                            isset($errors[$controllerModule]) ||
                            (
                                $moduleName == 'admin' &&
                                $action->getRequest()->getControllerName() == 'dashboard'
                            )
                        )
                    ) {
                        if ($errorSize == 1) {
                            $this->messageManager->addError(__(
                                'The extension: %1 is not activated. Please enter the license key and active it. %2',
                                '<strong>'.implode(', ', $errors).'</strong>',
                                '<a href="'.$action->getUrl('vnecoms/licenses').'">'.__('Manage Licenses').'</a>'
                            ));
                        } elseif ($errorSize > 1) {
                            $this->messageManager->addError(__(
                                'These extensions are not activated:%1.<br />Please enter the license key and active them. %2',
                                '<br /><strong>'.implode('<br />', $errors).'</strong>',
                                '<a href="'.$action->getUrl('vnecoms/licenses').'">'.__('Manage Licenses').'</a>'
                            ));
                        }
                    }

                    /* Redirect admin to license page in these case:
                     * - Admin first time login to admin panel
                     * - Admin access to an inactive extension
                     */
                    if (
                        $moduleName != 'vnecoms' &&
                        (
                            !$this->_session->getData('vnecoms_core_notify') ||
                            isset($errors[$controllerModule])
                        )
                    ) {
                        $action->getResponse()->setRedirect($action->getUrl('vnecoms/licenses'));
                        $this->_actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);
                        $this->_session->setData('vnecoms_core_notify', true);
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
    }

    /**
     * Check licenses.
     * 
     * @return multitype:string
     */
    public function checkLicense()
    {
        $errors = [];
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $deployConfig = $om->create('Magento\Framework\App\DeploymentConfig');
        $modules = $deployConfig->get(ConfigOptionsListConstants::KEY_MODULES);
        foreach ($modules as $module => $isEnabled) {
            if (strpos($module, 'Vnecoms_') === false || !$isEnabled) {
                continue;
            }

            /*Check if the vnecoms module is activated*/
            $error = $this->checkModuleActiveStatus($module);
            if ($error) {
                $errors[$error['extension_name']] = $error['name'];
            }
        }

        return $errors;
    }

    /**
     * Get extensions list.
     * 
     * @return array:
     */
    public function getExtensionsList()
    {
        if (!$this->_extensionsList) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $license = $om->create('Vnecoms\Core\Model\Key');
            $this->_extensionsList = $license->getExtensionsList();
        }

        return $this->_extensionsList;
    }

    /**
     * Get license collection.
     * 
     * @return \Vnecoms\Core\Model\ResourceModel\Key\Collection
     */
    public function getLicenseCollection()
    {
        if (!$this->_licenseCollection) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $licenseCollection = $om->create('Vnecoms\Core\Model\Key')->getCollection();
            /* Update all licenses*/
            if ($this->getFrequency() + $this->getLastUpdate() <= time()) {
                foreach ($licenseCollection as $license) {
                    try {
                        $licenseInfo = $license->getKeyInfo($license->getLicenseKey());
                        $license->setData('license_info', $licenseInfo);
                        $license->save();
                    } catch (\Exception $e) {
                    }
                }

                $this->setLastUpdate();
            }

            $this->_licenseCollection = $licenseCollection;
        }

        return $this->_licenseCollection;
    }

    /**
     * Check module active status
     * Return false if there is no error.
     * 
     * @param string $moduleName
     *
     * @return bool|array
     */
    public function checkModuleActiveStatus($moduleName)
    {
        $extensions = $this->getExtensionsList();
        $currentServerDomain = $_SERVER['HTTP_HOST'];
        if (!isset($extensions[$moduleName]) || !$extensions[$moduleName]) {
            return false;
        }

        $licenseCollection = $this->getLicenseCollection();
        $isActived = false;
        foreach ($licenseCollection as $license) {
            $keyInfo = $license->getSavedKeyInfo();
            $licensedExtensions = isset($keyInfo['licensed_extensions']) ? $keyInfo['licensed_extensions'] : [];
            $domains = isset($keyInfo['domains']) ? $keyInfo['domains'] : [];
            if (
                in_array($moduleName, $licensedExtensions) &&
                $keyInfo['secure_key'] == $license->getSecureKey() &&
                in_array($currentServerDomain, $domains) &&
                $keyInfo['status']  == 2
            ) {
                $isActived = true;
                break;
            }
        }

        return $isActived ? false : [
            'extension_name' => $extensions[$moduleName]['extension_name'],
            'name' => $extensions[$moduleName]['name'],
        ];
    }

    /**
     * Retrieve Update Frequency.
     *
     * @return int
     */
    public function getFrequency()
    {
        return 604800; /*7 days*/
    }

    /**
     * Retrieve Last update time.
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return $this->_cacheManager->load('vnecoms_license_update');
    }

    /**
     * Set last update time (now).
     *
     * @return $this
     */
    public function setLastUpdate()
    {
        $this->_cacheManager->save(time(), 'vnecoms_license_update');

        return $this;
    }
}
