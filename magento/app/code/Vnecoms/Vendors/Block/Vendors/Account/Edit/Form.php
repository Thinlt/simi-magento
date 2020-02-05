<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Vendors\Block\Vendors\Account\Edit;

use Magento\Backend\Block\Widget\Form as WidgetForm;

class Form extends \Vnecoms\Vendors\Block\Vendors\Widget\Form\Generic
{
    /**
     * @var string
     */
    protected $_template = 'Vnecoms_Vendors::account/form.phtml';
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('vendor_form');
        $this->setTitle(__('Vendor Information'));
    }

    
    /**
     * @return WidgetForm
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $saveUrl = $this->_getSaveUrl();
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $saveUrl,
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ],
            ]
        );
        $form->setUseContainer(true);
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => '','class'=>'box box-primary']);
        
        $fieldset->addField('vendor_account_tabs', 'note', []);

        $tabBlock = $this->getLayout()->createBlock('Vnecoms\Vendors\Block\Vendors\Account\Edit\Form\Renderer\Tabs', 'tabs_field');
        $form->getElement('vendor_account_tabs')->setRenderer($tabBlock);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
    /**
     * Get Save Url
     * @return string
     */
    protected function _getSaveUrl()
    {
        return $this->getUrl('account/index/save');
    }
}
