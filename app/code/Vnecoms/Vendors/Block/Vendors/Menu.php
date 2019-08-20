<?php
/**
 * Copyright © Vnecoms. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Vendors;

class Menu extends \Magento\Backend\Block\Menu
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;
    
    /**
     * @var \Vnecoms\Vendors\Model\Menu\Config
     */
    protected $_vendorMenuConfig;
    
    /**
     * @var MenuItemChecker
     */
    private $menuItemChecker;
    
    /**
     * @var AnchorRenderer
     */
    private $anchorRenderer;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Vnecoms\Vendors\Model\Url $url
     * @param \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Backend\Model\Menu\Config $menuConfig
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Vnecoms\Vendors\Model\Session $vendorSession
     * @param \Vnecoms\Vendors\Model\Menu\Config $vendorMenuConfig
     * @param array $data
     * @param \Magento\Backend\Block\MenuItemChecker $menuItemChecker
     * @param \Magento\Backend\Block\AnchorRenderer $anchorRenderer
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Vnecoms\Vendors\Model\Url $url,
        \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Model\Menu\Config $menuConfig,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Vnecoms\Vendors\Model\Session $vendorSession,
        \Vnecoms\Vendors\Model\Menu\Config $vendorMenuConfig,
        array $data = [],
        \Magento\Backend\Block\MenuItemChecker $menuItemChecker = null,
        \Magento\Backend\Block\AnchorRenderer $anchorRenderer = null
    ) {
        $this->_vendorSession = $vendorSession;
        $this->_vendorMenuConfig = $vendorMenuConfig;
        $this->menuItemChecker =  $menuItemChecker;
        $this->anchorRenderer = $anchorRenderer;
        return parent::__construct(
            $context,
            $url,
            $iteratorFactory,
            $authSession,
            $menuConfig,
            $localeResolver,
            $data,
            $menuItemChecker,
            $anchorRenderer
        );
    }
    
    
    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = [
            'vendors_top_nav',
            $this->getActive(),
            $this->_vendorSession->getCustomerId(),
            $this->_localeResolver->getLocale(),
        ];
        // Add additional key parameters if needed
        $newCacheKeyInfo = $this->getAdditionalCacheKeyInfo();
        if (is_array($newCacheKeyInfo) && !empty($newCacheKeyInfo)) {
            $cacheKeyInfo = array_merge($cacheKeyInfo, $newCacheKeyInfo);
        }
        return $cacheKeyInfo;
    }
    /**
     * Render menu item anchor
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @param int $level
     * @param boolean $hasChildren
     * @return string
     */
    protected function _renderAnchor($menuItem, $level, $hasChildren = false)
    {
        $output = '<a href="' . ($menuItem->getUrl()?$menuItem->getUrl():"#") . '" ' . $this->_renderItemAnchorTitle($menuItem)
        . $this->_renderItemOnclickFunction($menuItem)
        . ' class="' . $this->_renderAnchorCssClass($menuItem, $level) . '">'
        . '<i class="'.($menuItem->getIconClass()?$menuItem->getIconClass():'fa fa-circle-o').'"></i>'
        . '<span>' . $this->_getAnchorLabel($menuItem) . '</span>'
        . ($hasChildren?'<i class="fa fa-angle-left pull-right"></i>':'')
        . '</a>';

    
        return $output;
    }
    
    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @param int $level
     * @param int $limit
     * @param $id int
     * @return string HTML code
     */
    protected function _addSubMenu($menuItem, $level, $limit, $id = null)
    {
        $output = '';
        if (!$menuItem->hasChildren()) {
            return $output;
        }
        $colStops = [];

    
        $output .= $this->renderNavigation($menuItem->getChildren(), $level + 1, $limit, $colStops);
        return $output;
    }
    
    /**
     * Render Navigation
     *
     * @param \Magento\Backend\Model\Menu $menu
     * @param int $level
     * @param int $limit
     * @param array $colBrakes
     * @return string HTML
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function renderNavigation($menu, $level = 0, $limit = 0, $colBrakes = [])
    {
        $itemPosition = 1;
        $outputStart = '<ul ' . (0 == $level ? 'id="vendor-nav" role="menubar"' : 'role="menu"') . ' class="'.(0 == $level?"sidebar-menu":"treeview-menu") . '" >';
        $output = '';
        if (0 == $level) {
            $outputStart.= '<li class="header">'.__("MAIN NAVIGATION").'</li>';
        }
        
        /** @var $menuItem \Magento\Backend\Model\Menu\Item  */
        foreach ($this->_getMenuIterator($menu) as $menuItem) {
            $menuId = $menuItem->getId();
            $itemName = substr($menuId, strrpos($menuId, '::') + 2);
            $itemClass = str_replace('_', '-', strtolower($itemName));
        
            if (is_array($colBrakes) && count($colBrakes) && $colBrakes[$itemPosition]['colbrake'] && $itemPosition != 1) {
                $output .= '</ul></li><li class="column"><ul role="menu">';
            }
        
            $id = $this->getJsId($menuItem->getId());
            $subMenu = $this->_addSubMenu($menuItem, $level, $limit, $id);
            $anchor = $this->anchorRenderer->renderAnchor($this->getActiveItemModel(), $menuItem, $level);
            $output .= '<li ' . $this->getUiId($menuItem->getId())
            . ' class="item-' . $itemClass . ' ' . $this->_renderItemCssClass($menuItem, $level)
            . ($level == 0 ? '" id="' . $id . '" aria-haspopup="true' : '')
            . '" role="menu-item">' . $anchor . $subMenu . '</li>';
            $itemPosition++;
        }
        
        if (is_array($colBrakes) && count($colBrakes) && $limit) {
            $output = '<li class="column"><ul role="menu">' . $output . '</ul></li>';
        }
        
        return $outputStart . $output . '</ul>';
    }
    
    /**
     * Get menu config model
     *
     * @return \Magento\Backend\Model\Menu
     */
    public function getMenuModel()
    {
        return $this->_vendorMenuConfig->getMenu();
    }
    
    /**
     * Render item css class
     *
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @param int $level
     * @return string
     */
    protected function _renderItemCssClass($menuItem, $level)
    {
        $isLast = 0 == $level && (bool)$this->getMenuModel()->isLast($menuItem) ? 'last' : '';
        $isItemActive = $this->menuItemChecker->isItemActive(
            $this->getActiveItemModel(),
            $menuItem,
            $level
        ) ? '_current _active active' : '';
    
        $output =  $isItemActive .
        ' ' .
        ($menuItem->hasChildren() ? 'parent' : '') .
        ' ' .
        $isLast .
        ' ' .
        'level-' .
        $level;
        return $output;
    }
    
    /**
     * Get the name of currently logged in vendor
     *
     * @return string
     */
    public function getVendorName()
    {
        return $this->_vendorSession->getCustomer()->getName();
    }
	
	/**
     * (non-PHPdoc)
     * @see \Magento\Framework\View\Element\AbstractBlock::getCacheTags()
     */
    protected function getCacheTags(){
        $tags = parent::getCacheTags();
        $tags[] = 'vendor_menu';
        $tags[] = 'vendor_menu_'.$this->_vendorSession->getCustomerId();
        return $tags;
    }
}
