<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Store;

class Import extends \Magento\Backend\Block\Widget\Form\Container {

    public function _construct() {
        parent::_construct();
        $this->_blockGroup = 'Simi_Simistorelocator';
        $this->_controller = 'adminhtml_store';
        $this->_mode = 'import';
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->updateButton('save', 'label', __('Import Stores'));
    }

}
