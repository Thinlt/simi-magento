<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Key;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * banner factory.
     *
     * @var \VnEcoms\AdvancedPdfProcessor\Model\TemplateFactory
     */
    protected $_keyFactory;

    /**
     * Registry object.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data            $backendHelper
     * @param \Vnecoms\PdfPro\Model\KeyFactory        $keyFactory
     * @param \Magento\Framework\Registry             $coreRegistry
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Vnecoms\PdfPro\Model\KeyFactory $keyFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_keyFactory = $keyFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('keyGrid');
        $this->setDefaultSort('priority');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_keyFactory->create()->getCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->_eventManager->dispatch('ves_pdfpro_grid_prepare_columns_before', ['block' => $this]);

        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'api_key',
            [
                'header' => __('Identifier'),
                'index' => 'api_key',
                'align' => 'left',
                'width' => '50px',
            ]
        );
/* 
        $this->addColumn('logo',
            array(
                'header' => __('Logo'),
                'index' => 'logo',
                'renderer' => '\Vnecoms\PdfPro\Block\Adminhtml\Key\Helper\Renderer\Image',
                'sortable' => false,
                'type' => 'string',
                'width' => '150px',
                'filter' => false,
            )
        );
 */
        /*
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('store_ids', array(
                'header' => __('Store view'),
                'align' => 'left',
                'index' => 'store_ids',
                'type' => 'store',
                'store_all' => true,
                'store_view' => true,
                'filter_index' => 'main_table.store_ids',
                'skipEmptyStoresLabel' => true,
                'filter_condition_callback' => array($this, 'filterByStoreId'),
            ));
        }

        $this->addColumn('customer_group_ids', array(
            'header' => __('Customer Group'),
            'index' => 'customer_group_ids',
            'type' => 'store',
            'store_all' => true,
            'store_view' => true,
            'sortable' => false,
            'filter' => false,
            'renderer' => 'Vnecoms\PdfPro\Block\Adminhtml\Key\Helper\Renderer\Group',
        ));

        $this->addColumn(
            'comment',
            [
                'header' => __('Comment'),
                'index' => 'comment',
                'align' => 'left',
                'width' => '300px',
            ]
        );

        $this->addColumn(
            'priority',
            [
                'header' => __('Priority'),
                'index' => 'priority',
                'align' => 'left',
                'width' => '30px',
            ]
        );

        $this->addColumn(
            'edit',
            array(
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => __('Edit'),
                        'url' => array(
                            'base' => '*/*/edit',
                            'params' => array('store' => $this->getRequest()->getParam('store')),
                        ),
                        'field' => 'id',
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            )
        );

        $this->_eventManager->dispatch('ves_pdfpro_grid_prepare_columns_after', ['block' => $this]);

        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportXml', __('XML'));
        $this->addExportType('*/*/exportExcel', __('Excel'));

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['id' => $row->getId()]
        );
    }

    public function filterByStoreId($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if (isset($value) && $value) {
            $collection->addFieldToFilter('main_table.store_ids', $value);
        }
    }
}
