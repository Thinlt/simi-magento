<?php

namespace Simi\Simistorelocator\Model\Store\Option;

class Country implements \Magento\Framework\Data\OptionSourceInterface, \Simi\Simistorelocator\Model\Data\Option\OptionHashInterface {

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    public $countryCollectionFactory;

    public function __construct(
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
    ) {
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray() {
        $option = [];
        /** @var \Magento\Directory\Model\ResourceModel\Country\Collection $collection */
        $collection = $this->countryCollectionFactory->create()->loadByStore();

        foreach ($collection as $item) {
            $option[] = ['label' => $item->getName(), 'value' => $item->getId()];
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
        /** @var \Magento\Directory\Model\ResourceModel\Country\Collection $collection */
        $collection = $this->countryCollectionFactory->create()->loadByStore();

        foreach ($collection as $item) {
            $option[$item->getId()] = $item->getName();
        }

        return $option;
    }
}
