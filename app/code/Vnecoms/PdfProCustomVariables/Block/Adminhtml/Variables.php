<?php

namespace Vnecoms\PdfProCustomVariables\Block\Adminhtml;

/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 23:21
 */

class Variables extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_variables';
        $this->_headerText = __('Manage Variables');
        $this->_addButtonLabel = __('Add New Variable');
        parent::_construct();
    }
}
