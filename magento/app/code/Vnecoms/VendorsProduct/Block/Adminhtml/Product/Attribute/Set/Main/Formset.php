<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Block\Adminhtml\Product\Attribute\Set\Main;

use Magento\Backend\Block\Widget\Form;

class Formset extends \Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Main\Formset
{
    /**
     * Prepares attribute set form
     *
     * @return void
     */
    protected function _prepareForm()
    {
        $data = $this->_setFactory->create()->load($this->getRequest()->getParam('id'));

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('set_name', ['legend' => __('Edit Attribute Set Name')]);
        $fieldset->addField(
            'attribute_set_name',
            'label',
            [
                'label' => __('Name'),
                'note' => __("%1Click here%2 to edit product attribute set", sprintf('<a href="%s" target="_blank">', $this->getEditAttributeSetUrl()), '</a>'),
                'name' => 'attribute_set_name',
                'required' => true,
                'class' => 'required-entry validate-no-html-tags',
                'value' => $data->getAttributeSetName()
            ]
        );

        if (!$this->getRequest()->getParam('id', false)) {
            $fieldset->addField('gotoEdit', 'hidden', ['name' => 'gotoEdit', 'value' => '1']);

            $sets = $this->_setFactory->create()->getResourceCollection()->setEntityTypeFilter(
                $this->_coreRegistry->registry('entityType')
            )->load()->toOptionArray();

            $fieldset->addField(
                'skeleton_set',
                'select',
                [
                    'label' => __('Based On'),
                    'name' => 'skeleton_set',
                    'required' => true,
                    'class' => 'required-entry',
                    'values' => $sets
                ]
            );
        }

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('set-prop-form');
        $form->setAction($this->getUrl('vendors/*/save'));
        
        $form->setOnsubmit('return false;');
        $this->setForm($form);
    }
    
    /**
     * Get Edit attribute set URL
     * @return string
     */
    public function getEditAttributeSetUrl()
    {
        return $this->getUrl('catalog/product_set/edit', ['id'=>$this->getRequest()->getParam('id')]);
    }
}
