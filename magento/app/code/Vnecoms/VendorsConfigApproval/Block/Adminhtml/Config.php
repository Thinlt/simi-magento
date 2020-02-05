<?php
namespace Vnecoms\VendorsConfigApproval\Block\Adminhtml;

class Config extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Vnecoms_VendorsConfigApproval';
        $this->_controller = 'adminhtml_config';
        $this->_headerText = __('Manage Pending Config');
        parent::_construct();
        $this->removeButton('add');
    }
    
    protected function _prepareLayout()
    {
        /* $addButtonProps = [
            'id' => 'add_new_block',
            'label' => __('Add New Block'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => 'Vnecoms\AutoRelatedProduct\Block\Widget\Button\SplitButton',
            'options' => $this->_getAddProductButtonOptions(),
        ];
        $this->buttonList->add('add_new', $addButtonProps); */
        return parent::_prepareLayout();
    }
}
