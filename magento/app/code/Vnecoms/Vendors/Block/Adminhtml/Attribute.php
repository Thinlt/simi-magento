<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Adminhtml;

/**
 * Adminhtml catalog product attributes block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Attribute extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_attribute';
        $this->_blockGroup = 'Vnecoms_Vendors';
        $this->_headerText = __('Vendors Attributes');
        $this->_addButtonLabel = __('Add New Attribute');
        parent::_construct();
    }
}
