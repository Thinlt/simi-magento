<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Store\Edit\Tab\ScheduleTab;

use Simi\Simistorelocator\Model\Schedule\Option\WeekdayStatus;

class TableGrid extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var array
     */
    public $itemsData = [];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Template\Context $context        [description]
     * @param \Magento\Backend\Helper\Data            $backendHelper  [description]
     * @param \Magento\Framework\Data\Collection      $dataCollection [description]
     * @param array                                   $data           [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
) {
        $this->objectManager = $objectManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return array
     */
    public function getItemsData() {
        return $this->itemsData;
    }

    /**
     * @param array $itemsData
     */
    public function setItemsData(array $itemsData = []) {
        $this->itemsData = $itemsData;

        return $this;
    }

    /**
     * [_construct description].
     *
     * @return [type] [description]
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('scheduleTableGrid');
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setUseAjax(false);
    }

    /**
     * get schedule model.
     *
     * @return \Simi\Simistorelocator\Model\Schedule
     */
    public function getSchedule() {
        if ($this->hasData('schedule_id')) {
            $scheduleId = $this->getData('schedule_id');
        } else {
            $scheduleId = $this->getRequest()->getParam('schedule_id');
        }

        /** @var \Simi\Simistorelocator\Model\Schedule $schedule */
        $schedule = $this->objectManager->create('Simi\Simistorelocator\Model\Schedule');
        $schedule->load($scheduleId);

        if (!$this->hasData('schedule')) {
            $this->setData('schedule', $schedule);
        }

        return $this->getData('schedule');
    }

    /**
     * Prepare html output.
     *
     * @return string
     */
    protected function _toHtml() {
        if (!$this->getSchedule()->getId()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * prepare items data.
     */
    protected function _prepareItemsData() {
        /** @var \Simi\Simistorelocator\Model\Schedule $schedule */
        $schedule = $this->getSchedule();

        if (!$schedule->getId()) {
            return $this;
        }

        $statusRow = ['row_id' => __('Status')];
        $openTimeRow = ['row_id' => __('Open Time')];
        $openBreakTimeRow = ['row_id' => __('Open Break Time')];
        $closeBreakTimeRow = ['row_id' => __('Close Break Time')];
        $closeTimeRow = ['row_id' => __('Close Time')];

        foreach ($schedule->getWeekdays() as $weekday) {
            if ($schedule->getData($weekday . '_status') == WeekdayStatus::WEEKDAY_STATUS_OPEN) {
                $statusRow[$weekday] = __('Open');
            } else {
                $statusRow[$weekday] = __('Close');
            }

            $openTimeRow[$weekday] = $schedule->getData($weekday . '_open');
            $openBreakTimeRow[$weekday] = $schedule->getData($weekday . '_open_break');
            $closeBreakTimeRow[$weekday] = $schedule->getData($weekday . '_close_break');
            $closeTimeRow[$weekday] = $schedule->getData($weekday . '_close');
        }

        $this->setItemsData([$statusRow, $openTimeRow, $openBreakTimeRow, $closeBreakTimeRow, $closeTimeRow]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection() {
        $this->_prepareItemsData();

        /** @var \Magento\Framework\Data\Collection $collection */
        $collection = $this->objectManager->create('Magento\Framework\Data\Collection');

        foreach ($this->getItemsData() as $item) {
            $collection->addItem(
                    $this->objectManager->create('\Magento\Framework\DataObject', ['data' => $item])
            );
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns() {
        $this->addColumn(
                'row_id', [
            'header' => __(''),
            'align' => 'center',
            'index' => 'row_id',
            'filter' => false,
            'sortable' => false,
            'header_css_class' => 'a-center',
                ]
        );

        $this->addColumn(
                'monday', [
            'header' => __('Monday'),
            'align' => 'center',
            'index' => 'monday',
            'filter' => false,
            'sortable' => false,
            'header_css_class' => 'a-center',
                ]
        );

        $this->addColumn(
                'tuesday', [
            'header' => __('Tuesday'),
            'align' => 'center',
            'index' => 'tuesday',
            'filter' => false,
            'sortable' => false,
            'header_css_class' => 'a-center',
                ]
        );

        $this->addColumn(
                'wednesday', [
            'header' => __('Wednesday'),
            'align' => 'center',
            'index' => 'wednesday',
            'filter' => false,
            'sortable' => false,
            'header_css_class' => 'a-center',
                ]
        );

        $this->addColumn(
                'thursday', [
            'header' => __('Thursday'),
            'align' => 'center',
            'index' => 'thursday',
            'filter' => false,
            'sortable' => false,
            'header_css_class' => 'a-center',
                ]
        );

        $this->addColumn(
                'friday', [
            'header' => __('Friday'),
            'align' => 'center',
            'index' => 'friday',
            'filter' => false,
            'sortable' => false,
            'header_css_class' => 'a-center',
                ]
        );

        $this->addColumn(
                'saturday', [
            'header' => __('Saturday'),
            'align' => 'center',
            'index' => 'saturday',
            'filter' => false,
            'sortable' => false,
            'header_css_class' => 'a-center',
                ]
        );

        $this->addColumn(
                'sunday', [
            'header' => __('Sunday'),
            'align' => 'center',
            'index' => 'sunday',
            'filter' => false,
            'sortable' => false,
            'header_css_class' => 'a-center',
                ]
        );

        return parent::_prepareColumns();
    }

    /**
     * get row url.
     *
     * @param object $row
     *
     * @return string
     */
    public function getRowUrl($row) {
        return '';
    }
}
