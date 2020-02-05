<?php

namespace Vnecoms\PdfPro\Block\Adminhtml;

/**
 * Class Template.
 */
class Template extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_template';
        $this->_blockGroup = 'Vnecoms_PdfPro';
        $this->_headerText = __('Purchased Themes Manager');
        $this->_addButtonLabel = __('Add Theme');
        parent::_construct();
    }
}
