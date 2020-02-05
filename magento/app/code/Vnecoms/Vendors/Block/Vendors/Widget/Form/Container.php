<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Vendors\Widget\Form;

/**
 * Adminhtml footer block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Container extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var string
     */
    protected $_template = 'Vnecoms_Vendors::widget/form/container.phtml';
    
    
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->updateButton('save', 'class', 'btn-primary fa fa-check-circle');
        $this->updateButton('reset', 'class', 'btn-warning fa fa-refresh');
        $this->updateButton('delete', 'class', 'btn-danger fa fa-trash');
        $this->updateButton('back', 'class', 'btn-github fa fa-chevron-circle-left');
    }
}
