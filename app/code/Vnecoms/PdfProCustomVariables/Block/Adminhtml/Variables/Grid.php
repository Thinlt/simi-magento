<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 04/02/2017
 * Time: 09:00
 */

namespace Vnecoms\PdfProCustomVariables\Block\Adminhtml\Variables;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /** @var \Vnecoms\PdfProCustomVariables\Model\ResourceModel\PdfproCustomVariables\CollectionFactory  */
    protected $customVariablesCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Vnecoms\PdfProCustomVariables\Model\ResourceModel\PdfproCustomVariables\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->customVariablesCollectionFactory = $collectionFactory;
    }

    protected function _prepareCollection()
    {
        $collection = $this->customVariablesCollectionFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('custom_variable_id', [
            'header'    => __('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'custom_variable_id',
        ]);
        $this->addColumn('variable_type', [
            'header'    => __('Type'),
            'width'     => '150px',
            'index'     => 'variable_type',
            'type'      => 'options',
            'options'   => [
                'attribute'  => __('Product Attribute'),
                'customer'   => __('Customer Attribute'),
                /*'static'     			=> Mage::helper('catalogrule')->__('Static'),*/
            ],
        ]);
        $this->addColumn('name', [
            'header'    => __('Variable Name'),
            'align'     =>'left',
            'index'     => 'name',
        ]);

        $this->addColumn(
            'action',
            [
                'header'    =>  __('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => [
                    [
                        'caption'   => __('Edit'),
                        'url'       => ['base'=> '*/*/edit'],
                        'field'     => 'custom_variable_id'
                    ]
                ],
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ]
        );

        return parent::_prepareColumns();
    }

    public function _prepareMassaction()
    {
        $this->setMassactionIdField('custom_variable_id');
        $this->getMassactionBlock()->setFormFieldName('pdfprocustomvariables');

        $this->getMassactionBlock()->addItem('delete', [
            'label' => __('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?')
        ]);
        return $this;
    }
}
