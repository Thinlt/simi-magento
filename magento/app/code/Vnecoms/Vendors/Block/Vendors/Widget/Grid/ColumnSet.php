<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Vendors\Widget\Grid;

class ColumnSet extends \Magento\Backend\Block\Widget\Grid\ColumnSet
{
    /**
     * @var string
     */
    protected $_template = 'Vnecoms_Vendors::widget/grid/column_set.phtml';

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Backend\Model\Widget\Grid\Row\UrlGeneratorFactory $generatorFactory
     * @param \Magento\Backend\Model\Widget\Grid\SubTotals $subtotals
     * @param \Magento\Backend\Model\Widget\Grid\Totals $totals
     * @param array $data
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Model\Widget\Grid\Row\UrlGeneratorFactory $generatorFactory,
        \Magento\Backend\Model\Widget\Grid\SubTotals $subtotals,
        \Magento\Backend\Model\Widget\Grid\Totals $totals,
        \Vnecoms\Vendors\Model\UrlInterface $urlInterface,
        array $data = []
    ) {
        $generatorClassName = 'Magento\Backend\Model\Widget\Grid\Row\UrlGenerator';
        if (isset($data['rowUrl'])) {
            $rowUrlParams = $data['rowUrl'];
            $rowUrlParams["urlModel"] = $urlInterface;

            if (isset($rowUrlParams['generatorClass'])) {
                $generatorClassName = $rowUrlParams['generatorClass'];
            }
            $this->_rowUrlGenerator = $generatorFactory->createUrlGenerator(
                $generatorClassName,
                ['args' => $rowUrlParams]
            );
        }

        $this->setFilterVisibility(
            array_key_exists('filter_visibility', $data) ? (bool)$data['filter_visibility'] : true
        );

        \Magento\Framework\View\Element\Template::__construct($context, $data);

        $this->setEmptyText(isset($data['empty_text']) ? $data['empty_text'] : __('We couldn\'t find any records.'));

        $this->setEmptyCellLabel(
            isset($data['empty_cell_label']) ? $data['empty_cell_label'] : __('We couldn\'t find any records.')
        );

        $this->setCountSubTotals(isset($data['count_subtotals']) ? (bool)$data['count_subtotals'] : false);
        $this->_subTotals = $subtotals;

        $this->setCountTotals(isset($data['count_totals']) ? (bool)$data['count_totals'] : false);
        $this->_totals = $totals;
    }
}
