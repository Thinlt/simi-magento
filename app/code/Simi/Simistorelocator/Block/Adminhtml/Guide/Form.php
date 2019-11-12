<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Guide;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form before rendering HTML.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('guide_');

        /*
         * General Instructions
         */
        $fieldset = $form->addFieldset(
            'general_fieldset',
            [
                'legend' => __('General Instructions'),
                'class' => 'guide-fieldset',
            ]
        );

        $fieldset->addField(
            'general_instructions',
            'text',
            [
                'name' => 'general_instructions',
                'label' => __('General Instructions'),
                'title' => __('General Instructions'),
            ]
        )->setRenderer($this->getChildBlock('guide.general'));

        /*
         * guide for google API
         */
        $fieldset = $form->addFieldset(
            'google_fieldset',
            [
                'legend' => __('Instructions to create Google Map API Key'),
                'class' => 'guide-fieldset',
            ]
        );

        $fieldset->addField(
            'google',
            'text',
            [
                'name' => 'google',
                'label' => __('Instructions to create Google Map API Key'),
                'title' => __('Instructions to create Google Map API Key'),
            ]
        )->setRenderer($this->getChildBlock('guide.google'));

        /*
         * guide for facebook API
         */
        $fieldset = $form->addFieldset(
            'facebook_fieldset',
            [
                'legend' => __('Instructions to create Facebook API Key'),
                'class' => 'guide-fieldset',
            ]
        );

        $fieldset->addField(
            'facebook',
            'text',
            [
                'name' => 'facebook',
                'label' => __('Instructions to create Facebook API Key'),
                'title' => __('Instructions to create Facebook API Key'),
            ]
        )->setRenderer($this->getChildBlock('guide.facebook'));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
