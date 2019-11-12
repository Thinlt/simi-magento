<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Widget\Grid\Column\Filter;

class Checkbox extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Checkbox {

    /**
     * checkboxe fitler value.
     */
    const CHECKBOX_YES = 1;
    const CHECKBOX_NO = 0;

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter
     */
    public $converter;

    /**
     * Checkbox constructor.
     *
     * @param \Magento\Backend\Block\Context                                       $context
     * @param \Magento\Framework\DB\Helper                                         $resourceHelper
     * @param \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter
     * @param array                                                                $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter,
        array $data = []
    ) {
        parent::__construct($context, $resourceHelper, $data);
        $this->converter = $converter;
    }

    /**
     * get search condition of checkbox column in_storelocator.
     *
     * @return array
     */
    public function getCondition() {
        $values = $this->converter->toFlatArray($this->getColumn()->getValues());

        if ($this->getValue()) {
            return [['in' => $values]];
        } else {
            return [['nin' => $values]];
        }
    }
}
