<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Widget;

use Simi\Simistorelocator\Block\Adminhtml\Widget\Grid\Column\Filter\Checkbox as FilterCheckbox;

class Grid extends \Magento\Backend\Block\Widget\Grid {

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter
     */
    public $converter;

    /**
     * @var \Simi\Simistorelocator\Helper\Data
     */
    public $storelocatorHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data            $backendHelper
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Simi\Simistorelocator\Helper\Data $storelocatorHelper,
        \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->storelocatorHelper = $storelocatorHelper;
        $this->converter = $converter;

        if ($this->hasData('serialize_grid') && count($this->getSelectedRows())) {
            $this->setDefaultFilter(
                    ['checkbox_id' => FilterCheckbox::CHECKBOX_YES]
            );
        }
    }

    /**
     * get selected row values.
     *
     * @return array
     */
    public function getSelectedRows() {
        $selectedValues = $this->converter->toFlatArray(
                $this->storelocatorHelper->getTreeSelectedValues()
        );

        return array_values($selectedValues);
    }

}
