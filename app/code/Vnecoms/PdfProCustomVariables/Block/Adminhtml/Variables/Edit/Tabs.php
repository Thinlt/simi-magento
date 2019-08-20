<?php

namespace Vnecoms\PdfProCustomVariables\Block\Adminhtml\Variables\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('variables_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Variables Information'));
    }
}
