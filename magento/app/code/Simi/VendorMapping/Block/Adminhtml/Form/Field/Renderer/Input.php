<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\VendorMapping\Block\Adminhtml\Form\Field\Renderer;

/**
 * HTML select element block
 */
class Input extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Set element's HTML ID
     *
     * @param string $elementId ID
     * @return $this
     */
    public function setId($elementId)
    {
        $this->setData('id', $elementId);
        return $this;
    }

    /**
     * Alias of setId method
     */
    public function setInputId($elementId)
    {
        return $this->setId($elementId);
    }

    public function setName($name){
        $this->setData('name', $name);
        return $this;
    }
    
    /**
     * Alias of setName method
     */
    public function setInputName($name){
        return $this->setName($name);
    }

    public function getName(){
        return $this->getData('name');
    }

    /**
     * Set element's CSS class
     *
     * @param string $class Class
     * @return $this
     */
    public function setClass($class)
    {
        $this->setData('class', $class);
        return $this;
    }

    /**
     * Set element's HTML title
     *
     * @param string $title Title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->setData('title', $title);
        return $this;
    }

    /**
     * Set currency code to input html
     * @param string $code Currency
     * @return $this
     */
    public function setCurrency($code)
    {
        $this->setData('currency', $code);
        return $this;
    }

    /**
     * Set column
     * @param string $code Column
     * @return $this
     */
    public function setColumn($column)
    {
        $this->setData('column', $column);
        return $this;
    }

    public function setColumnName($columnName)
    {
        $this->setData('columnName', $columnName);
        return $this;
    }

    /**
     * HTML ID of the element
     *
     * @return string
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * CSS class of the element
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getData('class');
    }

    /**
     * Returns HTML title of the element
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Returns currency of the element
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->getData('currency');
    }

    public function getColumn()
    {
        return $this->getData('column');
    }

    public function getColumnName()
    {
        return $this->getData('columnName');
    }

    /**
     * Render HTML
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        $column = $this->getColumn();
        $class = $this->getClass();

        $html = '<div class="cell-input-wrap" style="position: relative;">'
            .'<input type="text" name="' . $this->getName() .
            '" id="' . $this->getId() .
            '" value="<%- ' . $this->getColumnName() . ' %>" ' .
            '" title="' . $this->escapeHtml($this->getTitle()) .
            '" class="' . ($class ? $class : 'input-text') . ' ' . $this->getData('validate') .
            '" ' . ($column['size'] ? 'size="' . $column['size'] : '') .
            '" ' . (isset($column['style']) ? ' style="padding-left: 15px;' . $column['style'] . '"' : 'style="padding-left: 15px;"') .
            '" ' . $this->getExtraParams() .
            '/>';
        
        $html .= '<span style="position: absolute; top: 7px; width: 20px; display: block; text-align: center;">'.$this->getCurrency().'</span>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Alias for toHtml()
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }
}
