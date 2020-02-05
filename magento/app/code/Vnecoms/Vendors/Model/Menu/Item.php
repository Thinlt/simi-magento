<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\Menu;

class Item extends \Magento\Backend\Model\Menu\Item
{
    protected $_icon_class;
    
    /**
     * @var \Vnecoms\Vendors\Model\UrlInterface
     */
    protected $_vendorUrlModel;
    
    /**
     * System event manager
     *
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    
    /**
     *
     * @param \Magento\Backend\Model\Menu\Item\Validator $validator
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Backend\Model\MenuFactory $menuFactory
     * @param \Magento\Backend\Model\UrlInterface $urlModel
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Vnecoms\Vendors\Model\UrlInterface $vendorUrlModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\Menu\Item\Validator $validator,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\MenuFactory $menuFactory,
        \Magento\Backend\Model\UrlInterface $urlModel,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Manager $moduleManager,
        \Vnecoms\Vendors\Model\UrlInterface $vendorUrlModel,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $data = []
    ) {
        parent::__construct(
            $validator,
            $authorization,
            $scopeConfig,
            $menuFactory,
            $urlModel,
            $moduleList,
            $moduleManager,
            $data
        );
        $this->_eventManager = $eventManager;
        $this->_vendorUrlModel = $vendorUrlModel;
        $this->_icon_class = $this->_getArgument($data, 'icon');
    }
    
    /**
     * Retrieve icon class
     *
     * @return string
     */
    public function getIconClass()
    {
        return $this->_icon_class;
    }
    
    /**
     * Check whether item is allowed to the user
     *
     * @return bool
     */
    public function isAllowed()
    {
        $result = new \Magento\FrameWork\DataObject(['is_allowed' => true]);
        $this->_eventManager->dispatch(
            'ves_vendor_menu_check_acl',
            [
                'resource' => $this->_resource,
                'result' => $result
            ]
        );
        $permission = new \Vnecoms\Vendors\Model\AclResult();
        $this->_eventManager->dispatch(
            'ves_vendor_check_acl',
            [
                'resource' => $this->_resource,
                'permission' => $permission
            ]
        );
        return $result->getIsAllowed() && $permission->isAllowed();
    }
    
    /**
     * Check whether item is disabled. Disabled items are not shown to user
     *
     * @return bool
     */
    public function isDisabled()
    {
        return false;
    }
    
    /**
     * Retrieve menu item url
     *
     * @return string
     */
    public function getUrl()
    {
        if ((bool)$this->_action) {
            return $this->_vendorUrlModel->getUrl(
                (string)$this->_action,
                ['_cache_secret_key' => true]
            );
        }
        return '#';
    }
    
    /**
     * Get menu item data represented as an array
     *
     * @return array
     * @since 100.2.0
     */
    public function toArray()
    {
        $arrMenu = parent::toArray();
        $arrMenu['icon'] = $this->_icon_class;
        
        return $arrMenu;
    }
    
    public function __wakeup()
    {
        parent::__wakeup();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_vendorUrlModel = $objectManager->get('Vnecoms\Vendors\Model\UrlInterface');
        $this->_eventManager = $objectManager->get('Magento\Framework\Event\ManagerInterface');
    }
    
    public function __sleep()
    {
        $result = parent::__sleep();
        $result[] = '_icon_class';
        return $result;
    }
}
