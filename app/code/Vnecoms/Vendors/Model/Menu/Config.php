<?php
namespace Vnecoms\Vendors\Model\Menu;

class Config extends \Magento\Backend\Model\Menu\Config
{
    const CACHE_ID = 'vendor_menu_config';
    const CACHE_VENDOR_MENU_OBJECT = 'vendor_menu_object';
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Backend\Model\Menu\Config::_initMenu()
     */
    protected function _initMenu()
    {
        if (!$this->_menu) {
            $menu = $this->_menuFactory->create();
    
            $cache = $this->_configCacheType->load(self::CACHE_VENDOR_MENU_OBJECT);
            if ($cache) {
                $menu->unserialize($cache);
                $this->_menu = $menu;
                return;
            }
    
            $areaCode = $this->_appState->getAreaCode();
            $this->_director->direct(
                $this->_configReader->read($areaCode),
                $this->_menuBuilder,
                $this->_logger
            );
            $menu = $this->_menuBuilder->getResult($menu);
            $this->_menu = $menu;
            $this->_configCacheType->save($menu->serialize(), self::CACHE_VENDOR_MENU_OBJECT);
        }
    }
}
