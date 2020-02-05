<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Block\Adminhtml\Vendor\Edit\Tab\Products;

/**
 * Customer Credit transactions grid
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends \Magento\Backend\Block\Dashboard\Grid
{
    protected $_template = 'Magento_Backend::widget/grid.phtml';
    
    /**
     * @var \Vnecoms\Credit\Model\ResourceModel\Credit\Transaction\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    
    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_productTypes;

    /**
     * @var \Magento\Catalog\Model\Product\AttributeSet\Options
     */
    protected $_attributeSetOptions;
    
    /**
     * @var \Vnecoms\VendorsProduct\Model\Source\Approval
     */
    protected $_approval;
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Product\Type $productType,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\Product\AttributeSet\Options $attributeSetOptions,
        \Vnecoms\VendorsProduct\Model\Source\Approval $approval,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_productTypes = $productType;
        $this->_attributeSetOptions = $attributeSetOptions;
        $this->_approval = $approval;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultLimit(20);
        $this->setPagerVisibility(true);
        $this->setFilterVisibility(true);
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $collection->addAttributeToSelect([
            'name',
            'status',
            'approval',
        ])->joinAttribute(
            'price',
            'catalog_product/price',
            'entity_id',
            null,
            'left'
        )->joinField(
            'qty',
            $collection->getTable('cataloginventory_stock_item'),
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        )->addAttributeToFilter('vendor_id', $this->getVendor()->getId());
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * Get current vendor object
     *
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor()
    {
        return $this->_coreRegistry->registry('current_vendor');
    }
    
    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID #'),
                'sortable' => true,
                'type' => 'number',
                'index' => 'entity_id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'sortable' => true,
                'type' => 'text',
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'type_id',
            [
                'header' => __('Type'),
                'sortable' => true,
                'type' => 'options',
                'index' => 'type_id',
                'options' => $this->_productTypes->getOptionArray(),
            ]
        );
        $attributeSetOpts = [];
        
        foreach ($this->_attributeSetOptions->toOptionArray() as $opt) {
            $attributeSetOpts[$opt['value']] = $opt['label'];
        }
        $this->addColumn(
            'attribute_set_id',
            [
                'header' => __('Attribute Set'),
                'sortable' => true,
                'type' => 'options',
                'index' => 'attribute_set_id',
                'options' => $attributeSetOpts,
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'sortable' => true,
                'type' => 'text',
                'index' => 'sku'
            ]
        );
        $currencyCode = $this->_storeManager->getStore(0)->getCurrentCurrencyCode();

        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'sortable' => true,
                'type' => 'currency',
                'currency_code' => $currencyCode,
                'index' => 'price'
            ]
        );
        $this->addColumn(
            'qty',
            [
                'header' => __('QTY'),
                'sortable' => true,
                'type' => 'number',
                'index' => 'qty'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'sortable' => true,
                'type' => 'options',
                'index' => 'status',
                'options' => [
                    1 => __("Enabled"),
                    2 => __("Disabled"),
                ],
            ]
        );
        $this->addColumn(
            'approval',
            [
                'header' => __('Approval'),
                'sortable' => true,
                'type' => 'options',
                'index' => 'approval',
                'options' => $this->_approval->getOptionArray(),
                'renderer' => 'Vnecoms\VendorsProduct\Block\Adminhtml\Vendor\Edit\Tab\Products\Approval',
            ]
        );
        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'sortable' => false,
                'filter' => false,
                'type' => 'action',
                'index' => 'entity_id',
                'actions'   => [
                    [
                        'caption'   => __('Edit'),
                        'url'       => ['base'=> 'catalog/product/edit'],
                        'field'     => 'id',
                    ]
                ],
            ]
        );

        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('vendors/catalog_product/grid', ['_current'=>true]);
    }
}
