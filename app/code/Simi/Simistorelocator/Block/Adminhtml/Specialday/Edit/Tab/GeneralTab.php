<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Specialday\Edit\Tab;

class GeneralTab extends \Magento\Backend\Block\Widget\Form\Generic
        implements \Magento\Backend\Block\Widget\Tab\TabInterface {

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm() {
        $model = $this->getRegistryModel();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('specialday_');

        $fieldset = $form->addFieldset('general_fieldset', ['legend' => __('General Information')]);

        if ($model->getId()) {
            $fieldset->addField('specialday_id', 'hidden', ['name' => 'specialday_id']);
        }

        $fieldset->addField(
                'specialday_name', 'text', [
            'name' => 'specialday_name',
            'label' => __('Special day Name'),
            'title' => __('Special day Name'),
            'required' => true,
                ]
        );

        $dateFormat = 'MM/dd/yyyy';
        $style = 'color: #000;background-color: #fff; font-weight: bold; font-size: 13px;';
        $fieldset->addField(
                'date_from', 'date', [
            'name' => 'date_from',
            'label' => __('Date Start'),
            'title' => __('Date Start'),
            'required' => true,
            'readonly' => true,
            'style' => $style,
            'class' => 'required-entry',
            'date_format' => $dateFormat,
                ]
        );

        $fieldset->addField(
                'date_to', 'date', [
            'name' => 'date_to',
            'label' => __('Date End'),
            'title' => __('Date End'),
            'required' => true,
            'readonly' => true,
            'style' => $style,
            'class' => 'required-entry',
            'date_format' => $dateFormat,
                ]
        );

        $fieldset->addField(
                'time_open', 'Simi\Simistorelocator\Block\Adminhtml\Widget\Form\Element\Time', [
            'name' => 'time_open',
            'label' => __('Open Time'),
            'title' => __('Open Time'),
            'style' => $style,
            'class' => 'required-entry',
            'date_format' => $dateFormat,
                ]
        );

        $fieldset->addField(
                'time_close', 'Simi\Simistorelocator\Block\Adminhtml\Widget\Form\Element\Time', [
            'name' => 'time_close',
            'label' => __('Close Time'),
            'title' => __('Close Time'),
            'style' => $style,
            'class' => 'required-entry',
            'date_format' => $dateFormat,
                ]
        );

        $fieldset->addField(
                'specialday_comment', 'editor', [
            'name' => 'specialday_comment',
            'label' => __('Comment'),
            'title' => __('Comment'),
            'wysiwyg' => true,
                ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * get registry model.
     *
     * @return \Simi\Simistorelocator\Model\Store | null
     */
    public function getRegistryModel() {
        return $this->_coreRegistry->registry('simistorelocator_specialday');
    }

    /**
     * Return Tab label.
     *
     * @return string
     *
     * @api
     */
    public function getTabLabel() {
        return __('General information');
    }

    /**
     * Return Tab title.
     *
     * @return string
     *
     * @api
     */
    public function getTabTitle() {
        return __('General information');
    }

    /**
     * Can show tab in tabs.
     *
     * @return bool
     *
     * @api
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Tab is hidden.
     *
     * @return bool
     *
     * @api
     */
    public function isHidden() {
        return false;
    }
}
