<?php
namespace Vnecoms\Vendors\Block\Vendors\Menu;

use Magento\Backend\Model\Menu\Item;
use Magento\Framework\Escaper;
use Magento\Backend\Block\MenuItemChecker;

/**
 * Class AnchorRenderer
 */
class AnchorRenderer extends \Magento\Backend\Block\AnchorRenderer
{
    /**
     * @var MenuItemChecker
     */
    private $menuItemChecker;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param MenuItemChecker $menuItemChecker
     * @param Escaper $escaper
     */
    public function __construct(
        MenuItemChecker $menuItemChecker,
        Escaper $escaper
    ) {
        $this->menuItemChecker = $menuItemChecker;
        $this->escaper = $escaper;
    }

    /**
     * Render menu item anchor.
     *
     *  It is used in backend menu to render anchor menu.
     *
     * @param Item|false $activeItem Can be false if menu item is inaccessible
     * but was triggered directly using controller. It is a legacy code behaviour.
     * @param Item $menuItem
     * @param int $level
     * @return string
     */
    public function renderAnchor($activeItem, Item $menuItem, $level)
    {
        if ($level == 1 && $menuItem->getUrl() == '#') {
            $output = '';
            if ($menuItem->hasChildren()) {
                $output = '<a href="#" onclick="return false;">'
                    . '<i class="'.($menuItem->getIconClass()?$menuItem->getIconClass():'fa fa-circle-o').'"></i>'
                    . '<span>' . $this->escaper->escapeHtml(__($menuItem->getTitle())) . '</span>'
                    . '<i class="fa fa-angle-left pull-right"></i>'
                    . '</a>';
            }
        } else {
            $target = $menuItem->getTarget() ? ('target=' . $menuItem->getTarget()) : '';
            $output = '<a href="' . $menuItem->getUrl() . '" ' . $target . ' ' . $this->_renderItemAnchorTitle(
                $menuItem
            ) . $this->_renderItemOnclickFunction(
                $menuItem
            ) . ' class="' . ($this->menuItemChecker->isItemActive($activeItem, $menuItem, $level) ? '_active' : '')
                . '">' 
                . '<i class="'.($menuItem->getIconClass()?$menuItem->getIconClass():'fa fa-circle-o').'"></i>'
                . '<span>' . $this->escaper->escapeHtml(__($menuItem->getTitle()))
                . '</span>' 
                . ($menuItem->hasCHildren()?'<i class="fa fa-angle-left pull-right"></i>':'')

                . '</a>';
        }

        return $output;
    }

    /**
     * Render menu item anchor title
     *
     * @param Item $menuItem
     * @return string
     */
    private function _renderItemAnchorTitle($menuItem)
    {
        return $menuItem->hasTooltip() ? 'title="' . __($menuItem->getTooltip()) . '"' : '';
    }

    /**
     * Render menu item onclick function
     *
     * @param Item $menuItem
     * @return string
     */
    private function _renderItemOnclickFunction($menuItem)
    {
        return $menuItem->hasClickCallback() ? ' onclick="' . $menuItem->getClickCallback() . '"' : '';
    }
}
