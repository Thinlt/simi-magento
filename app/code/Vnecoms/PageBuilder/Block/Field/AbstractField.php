<?php
namespace Vnecoms\PageBuilder\Block\Field;

class AbstractField extends \Magento\Framework\View\Element\Template
{
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\View\Element\Template::_toHtml()
     */
    protected function _toHtml(){
        if(!$this->getIsActive()) return '';
        return parent::_toHtml();
    }
}
