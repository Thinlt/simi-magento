<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Template;

/**
 * Class Grid.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * banner factory.
     *
     * @var \Vnecoms\PdfPro\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * Registry object.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Template\Context $context         [description]
     * @param \Magento\Backend\Helper\Data            $backendHelper   [description]
     * @param \Vnecoms\PdfPro\Model\TemplateFactory   $templateFactory [description]
     * @param \Magento\Framework\Registry             $coreRegistry    [description]
     * @param array                                   $data            [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Vnecoms\PdfPro\Model\TemplateFactory $templateFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('templateGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_templateFactory->create()->getCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'preview_image',
            [
                'header' => __('Preview Image'),
                'index' => 'preview_image',
                'width' => '100px',
                'filter' => false,
                'renderer' => '\Vnecoms\PdfPro\Block\Adminhtml\Template\Grid\Helper\Renderer\Image',

            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'width' => '50px',
                'index' => 'sku',
            ]
        );
        $this->addColumn(
            'css_path',
            [
                'header' => __('CSS Path'),
                'index' => 'css_path',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => '*/*/edit'],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );
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
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('template');

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
}
