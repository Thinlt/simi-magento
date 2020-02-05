<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Store\Import;

use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Class Tab GeneralTab
 */
class Form extends Generic {

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm() {

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
                [
                    'data' => [
                        'id' => 'edit_form',
                        'action' => $this->getUrl('*/*/importProcess'),
                        'method' => 'post',
                        'enctype' => 'multipart/form-data',
                    ],
                ]
        );

        $fieldset = $form->addFieldset('general_fieldset', ['legend' => __('Import Information')]);

        $fieldset->addField(
                'filecsv', 'file', [
            'title' => __('Import File'),
            'label' => __('Import File'),
            'name' => 'filecsv',
            'required' => true,
            'note' => 'Only csv file is supported. Click <a target="_blank" href="'
            . $this->getUrl('simistorelocatoradmin/store/sampleFile')
            . '">here</a> to download the Sample CSV file',
                ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
