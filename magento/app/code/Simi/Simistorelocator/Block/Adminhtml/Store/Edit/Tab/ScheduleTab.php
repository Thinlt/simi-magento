<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Store\Edit\Tab;

class ScheduleTab extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

    /**
     * @var \Simi\Simistorelocator\Model\Store\Option\Schedule
     */
    public $scheduleOption;

    /**
     * ScheduleTab constructor.
     *
     * @param \Magento\Backend\Block\Template\Context             $context
     * @param \Magento\Framework\Registry                         $registry
     * @param \Magento\Framework\Data\FormFactory                 $formFactory
     * @param \Simi\Simistorelocator\Model\Store\Option\Schedule $scheduleOption
     * @param array                                               $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Simi\Simistorelocator\Model\Store\Option\Schedule $scheduleOption,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->scheduleOption = $scheduleOption;
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm() {
        /** @var \Simi\Simistorelocator\Model\Store $model */
        $model = $this->getRegistryModel();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('store_');

        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->addFieldset(
                'scheudle_fieldset', [
            'legend' => __('Time Schedule'),
                ]
        );

        $fieldset->addField(
                'schedule_id', 'select', [
            'name' => 'schedule_id',
            'label' => __('Schedule'),
            'title' => __('Schedule'),
            'values' => array_merge(
                    [
                ['value' => '', 'label' => __('-------- Please select a Schedule --------')],
                    ], $this->scheduleOption->toOptionArray()
            ),
            'note' => $this->_getNoteCreateSchedule(),
                ]
        );

        /** @var \Simi\Simistorelocator\Block\Adminhtml\Store\Edit\Tab\ScheduleTab\Renderer\ScheduleTable $scheduleTableBlock */
        $scheduleTableBlock = $this->getLayout()
                ->createBlock('Simi\Simistorelocator\Block\Adminhtml\Store\Edit\Tab\ScheduleTab\Renderer\ScheduleTable');

        $fieldset->addField(
                'schedule_table', 'text', [
            'name' => 'schedule_table',
            'label' => __('Schedule Table'),
                ]
        )->setRenderer($scheduleTableBlock);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * get note create new schedule.
     *
     * @return mixed
     */
    protected function _getNoteCreateSchedule() {
        return sprintf(
                '<a href="%s" target="_blank">%s</a> %s',
                $this->getUrl('simistorelocatoradmin/schedule/new'),
                __('Click here'), __('to go to page create new schedule.')
        );
    }

    /**
     * get registry model.
     *
     * @return \Simi\Simistorelocator\Model\Store
     */
    public function getRegistryModel() {
        return $this->_coreRegistry->registry('simistorelocator_store');
    }

    /**
     * Return Tab label.
     *
     * @return string
     *
     * @api
     */
    public function getTabLabel() {
        return __('Store\'s Schedule');
    }

    /**
     * Return Tab title.
     *
     * @return string
     *
     * @api
     */
    public function getTabTitle() {
        return __('Store\'s Schedule');
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
