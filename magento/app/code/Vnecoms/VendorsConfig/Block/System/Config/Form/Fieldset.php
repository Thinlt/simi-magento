<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Config form fieldset renderer
 */
namespace Vnecoms\VendorsConfig\Block\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Fieldset extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    protected $isCollapsedDefault = true;
    
    /**
     * Return header html for fieldset
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getHeaderHtml($element)
    {
        $html = $element->getIsNested()?
            '<tr class="nested"><td colspan="4"><div class="' . $this->_getFrontendClass($element) . '">':
            '<div class="' . $this->_getFrontendClass($element) . '">';
        
        $html .= '<div class="entry-edit-head admin__collapsible-block box-header with-border">' .
            '<span id="' .
            $element->getHtmlId() .
            '-link" class="entry-edit-head-link"></span>';
        $html .= '<i class="fa fa-bars"></i>';
        $html .= $this->_getHeaderTitleHtml($element);
    
        $html .= '</div>';
        $html .= '<input id="' .
            $element->getHtmlId() .
            '-state" name="config_state[' .
            $element->getId() .
            ']" type="hidden" value="' .
            (int)$this->_isCollapseState(
                $element
            ) . '" />';
        $html .= '<fieldset class="' . $this->_getFieldsetCss() . '" id="' . $element->getHtmlId() . '">';
        $html .= '<legend>' . $element->getLegend() . '</legend>';
    
        $html .= $this->_getHeaderCommentHtml($element);
    
        // field label column
        $html .= '<div class="box-body form-horizontal">';

        return $html;
    }
    
    /**
     * Return footer html for fieldset
     * Add extra tooltip comments to elements
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getFooterHtml($element)
    {
        $html = '</div>';
        foreach ($element->getElements() as $field) {
            if ($field->getTooltip()) {
                $html .= sprintf(
                    '<div id="row_%s_comment" class="system-tooltip-box" style="display:none;">%s</div>',
                    $field->getId(),
                    $field->getTooltip()
                );
            }
        }
        $html .= '</fieldset>' . $this->_getExtraJs($element);
    
        $html .= $element->getIsNested()?'</td></tr>':'</div>';
        
        return $html;
    }
    
    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        $html = '';
        
        $transport = new \Magento\Framework\DataObject([
            'fieldset' => $element,
            'html' => $html,
            'force_return' => false
        ]);
        $this->_eventManager->dispatch(
            'ves_vendorsconfig_form_fieldset_prepare_before',
            ['transport' => $transport]
        );

        $html = $transport->getHtml();
        if ($transport->getForceReturn()) {
            return $html;
        }
        
        $html = $this->_getHeaderHtml($element);
        $elementCount = 0;
        foreach ($element->getElements() as $field) {
            if ($field->getIsRemoved()) {
                continue;
            }
            $elementCount ++;
            $html .= ($field instanceof \Magento\Framework\Data\Form\Element\Fieldset)?
                '<div id="row_' . $field->getHtmlId() . '">' . $field->toHtml() . '</div>':
                $field->toHtml();
            
        }
        
        /*Return nothing if there is no field*/
        if (!$elementCount) {
            return '';
        }
        
        $html .= $this->_getFooterHtml($element);
    
        return $html;
    }
    
    /**
     * Return header title part of html for fieldset
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        return '<a class="box-title" id="' .
            $element->getHtmlId() .
            '-head" href="#' .
            $element->getHtmlId() .
            '-link" onclick="Fieldset.toggleCollapse(\'' .
            $element->getHtmlId() .
            '\'); return false;">' . $element->getLegend() . '</a>';
    }


    /**
     * Collapsed or expanded fieldset when page loaded?
     *
     * @param AbstractElement $element
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _isCollapseState($element)
    {
        return $this->isCollapsedDefault;
    }
}
