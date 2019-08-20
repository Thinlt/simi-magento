<?php

namespace Vnecoms\PdfPro\Block\Adminhtml;

class Key extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_key';
        $this->_blockGroup = 'Vnecoms_PdfPro';
        $this->_headerText = __('PDF Template Manager');
        $this->_addButtonLabel = __('Add New Template');
        parent::_construct();

        /*$this->addButton('check_for_update', array(
            'label'     => __('Check For Upgrades'),
            'onclick'   => 'setLocation(\'' . $this->getCheckUpdateUrl() .'\')',
            'class'     => 'loading',
        ));*/
    }

    public function getCheckUpdateUrl()
    {
        return $this->getUrl('*/*/check');
    }
}
