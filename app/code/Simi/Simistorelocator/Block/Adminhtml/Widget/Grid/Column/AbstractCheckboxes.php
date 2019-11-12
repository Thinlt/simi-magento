<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Widget\Grid\Column;

abstract class AbstractCheckboxes extends \Magento\Backend\Block\Widget\Grid\Column {

    /**
     * @var \Simi\Simistorelocator\Helper\Data
     */
    public $storelocatorHelper;

    /**
     * @var \Simi\Simistorelocator\Model\StoreFactory
     */
    public $storeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Simi\Simistorelocator\Helper\Data $storelocatorHelper,
        \Simi\Simistorelocator\Model\StoreFactory $storeFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storelocatorHelper = $storelocatorHelper;
        $this->storeFactory = $storeFactory;

        $this->_filterTypes['checkbox'] = 'Simi\Simistorelocator\Block\Adminhtml\Widget\Grid\Column\Filter\Checkbox';
    }

    /**
     * values.
     *
     * @return mixed
     */
    public function getValues() {
        if (!$this->hasData('values')) {
            $this->setData('values', $this->getSelectedValues());
        }

        return $this->getData('values');
    }

    /**
     * get selected rows.
     *
     * @return array
     */
    abstract public function getSelectedValues();
}
