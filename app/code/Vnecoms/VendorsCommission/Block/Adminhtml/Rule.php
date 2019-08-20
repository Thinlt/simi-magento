<?php
/**
 * Catalog price rules
 *
 * @author      Vnecoms Team <core@vnecoms.com>
 */
namespace Vnecoms\VendorsCommission\Block\Adminhtml;

class Rule extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Vnecoms_VendorsCommission';
        $this->_controller = 'adminhtml_rule';
        $this->_headerText = __('Manage Commission Rules');
        parent::_construct();
        $this->_addButtonLabel = __('Add new Rule');
    }
}
