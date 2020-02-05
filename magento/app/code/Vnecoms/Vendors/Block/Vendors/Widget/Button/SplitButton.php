<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Vendors\Widget\Button;

class SplitButton extends \Magento\Ui\Component\Control\SplitButton
{
    /**
     * Define block template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Vnecoms_Vendors::widget/button/split.phtml');
    }
    /**
     * Get Button class
     * @see \Magento\Backend\Block\Widget\Button\SplitButton::getClass()
     */
    public function getClass(){
        return 'btn btn-default '.$this->getData('class');
    }
    /**
     * @see \Magento\Backend\Block\Widget\Button\SplitButton::getButtonClass()
     */
    public function getButtonClass(){
        return 'btn-group '.$this->getData('button_class');
    }

    /**
     * Retrieve toggle button attributes html
     *
     * @return string
     */
    public function getToggleAttributesHtml()
    {
        $disabled = $this->getDisabled() ? 'disabled' : '';
        $classes = ['action-toggle', 'primary'];

        if (!($title = $this->getTitle())) {
            $title = $this->getLabel();
        }

        if (($currentClass = $this->getClass())) {
            $classes[] = $currentClass;
        }

        if ($disabled) {
            $classes[] = $disabled;
        }

        $attributes = ['title' => $title, 'class' => join(' ', $classes), 'disabled' => $disabled];
        $this->getDataAttributes(['mage-init' => '{"dropdown": {}}', 'toggle' => 'dropdown'], $attributes);

        $html = $this->attributesToHtml($attributes);
        $html .= $this->getUiId('dropdown');

        return $html;
    }
}
