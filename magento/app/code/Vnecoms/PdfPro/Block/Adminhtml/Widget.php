<?php

namespace Vnecoms\PdfPro\Block\Adminhtml;

/**
 * Class Widget.
 */
class Widget extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * 
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Vnecoms_PdfPro';
        $this->_controller = 'adminhtml';
        $this->_mode = 'widget';
        $this->_headerText = __('Widget Insertion');

        $this->removeButton('reset');
        $this->removeButton('back');
        $this->buttonList->update('save', 'label', __('Insert Widget'));
        $this->buttonList->update('save', 'class', 'action-primary add-widget');
        $this->buttonList->update('save', 'id', 'insert_button');
        $this->buttonList->update('save', 'onclick', 'wWidget.insertWidget()');
        $this->buttonList->update('save', 'region', 'footer');
        $this->buttonList->update('save', 'data_attribute', []);

        $this->_formScripts[] = 'require(["mage/adminhtml/wysiwyg/widget","pdfWidget"], function(){wWidget = new WysiwygWidget.Widget('.
            '"widget_options_form", "select_widget_type", "widget_options", "'.
            $this->getUrl(
                '*/*/loadOptions'
            ).'", "'.$this->getRequest()->getParam(
                'widget_target_id'
            ).'");});';
    }
}
