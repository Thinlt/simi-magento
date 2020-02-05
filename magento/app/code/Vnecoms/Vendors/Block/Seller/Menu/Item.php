<?php
namespace Vnecoms\Vendors\Block\Seller\Menu;

/**
 * Class View
 * @package Vnecoms\Vendors\Block\Profile\Content
 */
class Item extends \Magento\Framework\View\Element\Html\Link\Current
{
    protected $_template = 'Vnecoms_Vendors::seller/menu/item.phtml';
    
    protected $_childrenItems;
    
    /**
     * GEt Chilndren Items
     *
     * @return multitype:
     */
    public function getChildrenItems()
    {
        if (!$this->_childrenItems) {
            $this->_childrenItems = $this->_layout->getChildBlocks($this->getNameInLayout());
        }
        
        return $this->_childrenItems;
    }
    
    /**
     * Get href URL
     *
     * @return string
     */
    public function getHref()
    {
        if (!$this->getPath() || $this->getPath() == '#') {
            return '#';
        }
        return $this->getUrl($this->getPath());
    }
    
    /**
     * Has Children Items
     *
     * @return boolean
     */
    public function hasChildItems()
    {
        return sizeof($this->getChildrenItems());
    }
    
    /**
     * Is Current
     *
     * @see \Magento\Framework\View\Element\Html\Link\Current::isCurrent()
     */
    public function isCurrent()
    {
        $currentItemIsAChildItem = false;
        foreach ($this->getChildrenItems() as $childItem) {
            if ($childItem->isCurrent()) {
                $currentItemIsAChildItem = true;
            }
        }
        return $currentItemIsAChildItem || parent::isCurrent();
    }
}
