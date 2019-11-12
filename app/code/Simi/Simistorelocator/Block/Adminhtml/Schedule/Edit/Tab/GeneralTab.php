<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Schedule\Edit\Tab;

use Simi\Simistorelocator\Model\Schedule\Option\WeekdayStatus;

class GeneralTab extends \Simi\Simistorelocator\Block\Adminhtml\Widget\Form
        implements \Magento\Backend\Block\Widget\Tab\TabInterface {

    const FORM_DATA_ELEMENT_TIME = 'Simi\Simistorelocator\Block\Adminhtml\Widget\Form\Element\Time';

    /**
     * weekday status.
     *
     * @var WeekdayStatus
     */
    public $weekdayStatus;

    /**
     * @var \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory
     */
    public $dependencyFieldFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                                $context
     * @param \Magento\Framework\Registry                                            $registry
     * @param \Magento\Framework\Data\FormFactory                                    $formFactory
     * @param WeekdayStatus                                                          $weekdayStatus
     * @param \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $dependencyFieldFactory
     * @param array                                                                  $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, WeekdayStatus $weekdayStatus, \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $dependencyFieldFactory, array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->weekdayStatus = $weekdayStatus;
        $this->dependencyFieldFactory = $dependencyFieldFactory;
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm() {
        /** @var \Simi\Simistorelocator\Model\Schedule $model */
        $model = $this->getRegistryModel();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('schedule_');

        $fieldset = $form->addFieldset(
                'general_fieldset', [
            'legend' => __('General Information'),
                ]
        );

        if ($model->getId()) {
            $fieldset->addField('schedule_id', 'hidden', ['name' => 'schedule_id']);
        }

        /*
         * dependence block
         */
        $dependenceBlock = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
        );

        $fieldset->addField(
                'schedule_name', 'text', [
            'name' => 'schedule_name',
            'label' => __('Schedule Name'),
            'title' => __('Schedule Name'),
            'required' => true,
                ]
        );

        /*
         * add field set each day in week
         */
        foreach ($model->getWeekdays() as $weekday) {
            $this->_addWeekdayFieldSet($form, $dependenceBlock, $weekday);
        }

        /*
         * add child block dependence
         */
        $this->setChild('form_after', $dependenceBlock);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * get dependency field.
     *
     * @return \Magento\Config\Model\Config\Structure\Element\Dependency\Field
     */
    protected function _getDependencyField($refField, $negative = false, $separator = ',', $fieldPrefix = '') {
        return $this->dependencyFieldFactory->create(
                        [
                            'fieldData' => [
                                'value' => (string) $refField,
                                'negative' => $negative,
                                'separator' => $separator,
                            ],
                            'fieldPrefix' => $fieldPrefix,
                        ]
        );
    }

    /**
     * @param \Magento\Framework\Data\Form $form
     * @param $weekday
     */
    public function _addWeekdayFieldSet(
    \Magento\Framework\Data\Form $form, \Magento\Backend\Block\Widget\Form\Element\Dependence $dependenceBlock, $weekday
    ) {
        $weekdayUpper = ucfirst($weekday);

        $fieldset = $form->addFieldset(
                $weekday . '_fieldset', [
            'legend' => __($weekdayUpper),
            'collapsable' => true,
            'apply_to_all' => ($weekday == 'monday') ? true : false,
                ]
        );

        /** @var \Magento\Framework\Data\Form\Element\AbstractElement[] $fields */
        $fields = [];

        $fields['status'] = $fieldset->addField(
                $weekday . '_status', 'select', [
            'name' => $weekday . '_status',
            'label' => __($weekdayUpper . ' Status'),
            'title' => __($weekdayUpper . ' Status'),
            'required' => true,
            'options' => $this->weekdayStatus->toOptionHash(),
                ]
        );

        $fields['open'] = $fieldset->addField(
                $weekday . '_open', self::FORM_DATA_ELEMENT_TIME, [
            'name' => $weekday . '_open',
            'label' => __('Open Time'),
            'title' => __('Open Time'),
            'required' => true,
                ]
        );

        $fields['open_break'] = $fieldset->addField(
                $weekday . '_open_break', self::FORM_DATA_ELEMENT_TIME, [
            'name' => $weekday . '_open_break',
            'label' => __('Open Break Time'),
            'title' => __('Open Break Time'),
            'required' => true,
                ]
        );

        $fields['close_break'] = $fieldset->addField(
                $weekday . '_close_break', self::FORM_DATA_ELEMENT_TIME, [
            'name' => $weekday . '_close_break',
            'label' => __('Close Break Time'),
            'title' => __('Close Break Time'),
            'required' => true,
                ]
        );

        $fields['close'] = $fieldset->addField(
                $weekday . '_close', self::FORM_DATA_ELEMENT_TIME, [
            'name' => $weekday . '_close',
            'label' => __('Close Time'),
            'title' => __('Close Time'),
            'required' => true,
                ]
        );

        /*
         * Add name => id mapping
         */
        foreach ($fields as $field) {
            $dependenceBlock->addFieldMap($field->getHtmlId(), $field->getName());
        }

        /*
         * Register field name dependence one from each other by specified values
         */

        $dependenceBlock->addFieldDependence(
                $fields['open']->getName(), $fields['status']->getName(), $this->_getDependencyField(WeekdayStatus::WEEKDAY_STATUS_OPEN)
        );

        $dependenceBlock->addFieldDependence(
                $fields['open_break']->getName(), $fields['status']->getName(), $this->_getDependencyField(WeekdayStatus::WEEKDAY_STATUS_OPEN)
        );

        $dependenceBlock->addFieldDependence(
                $fields['close_break']->getName(), $fields['status']->getName(), $this->_getDependencyField(WeekdayStatus::WEEKDAY_STATUS_OPEN)
        );

        $dependenceBlock->addFieldDependence(
                $fields['close']->getName(), $fields['status']->getName(), $this->_getDependencyField(WeekdayStatus::WEEKDAY_STATUS_OPEN)
        );

        return $fieldset;
    }

    /**
     * get registry model.
     *
     * @return \Simi\Simistorelocator\Model\Store | null
     */
    public function getRegistryModel() {
        return $this->_coreRegistry->registry('simistorelocator_schedule');
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
