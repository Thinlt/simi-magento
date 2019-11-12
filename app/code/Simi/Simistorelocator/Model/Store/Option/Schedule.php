<?php

namespace Simi\Simistorelocator\Model\Store\Option;

class Schedule implements \Magento\Framework\Data\OptionSourceInterface, \Simi\Simistorelocator\Model\Data\Option\OptionHashInterface {

    /**
     * @var \Simi\Simistorelocator\Model\ResourceModel\Schedule\CollectionFactory
     */
    public $scheduleCollectionFactory;

    public function __construct(
    \Simi\Simistorelocator\Model\ResourceModel\Schedule\CollectionFactory $scheduleCollectionFactory
    ) {
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
    }

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray() {
        $option = [];
        /** @var \Simi\Simistorelocator\Model\ResourceModel\Schedule\Collection $collection */
        $collection = $this->scheduleCollectionFactory->create();

        foreach ($collection as $schedule) {
            $option[] = ['label' => $schedule->getScheduleName(), 'value' => $schedule->getId()];
        }

        return $option;
    }

    /**
     * Return array of options as key-value pairs.
     *
     * @return array Format: array('<key>' => '<value>', '<key>' => '<value>', ...)
     */
    public function toOptionHash() {
        $option = [];
        /** @var \Simi\Simistorelocator\Model\ResourceModel\Schedule\Collection $collection */
        $collection = $this->scheduleCollectionFactory->create();

        foreach ($collection as $schedule) {
            $option[$schedule->getId()] = $schedule->getScheduleName();
        }

        return $option;
    }
}
