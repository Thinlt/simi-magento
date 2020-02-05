<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsDashboard\Block\Adminhtml\Dashboard\Bestseller;

use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Adminhtml seller dashboard recent transaction grid
 *
 */
class Grid extends \Magento\Backend\Block\Dashboard\Grid implements TabInterface
{
    protected $_template = 'Magento_Backend::dashboard/grid.phtml';
    
    /**
     * @var \Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_collectionFactory;


    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->coreRegistry = $coreRegistry;
        
        parent::__construct($context, $backendHelper, $data);
    }

    
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('bestSellerGrid');
    }

    /**
     * Prepare content for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabLabel()
    {
        return __('Bestseller');
    }
    
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Bestseller');
    }
    
    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }
    
    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }
    
    /**
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor()
    {
        return $this->coreRegistry->registry('current_vendor');
    }
    
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $connection = $collection->getConnection();
        
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToFilter('vendor_id', $this->getVendor()->getId());
        $resource = $collection->getResource();
        $collection->joinTable(
            ['order_items' => $resource->getTable('sales_order_item')],
            'product_id = entity_id',
            ['qty_ordered' => 'SUM(order_items.qty_ordered)'],
            null,
            'left'
        );
        
        $orderJoinCondition = [
            'order.entity_id = order_items.order_id',
            $connection->quoteInto("order.state <> ?", \Magento\Sales\Model\Order::STATE_CANCELED),
        ];
        
        $collection->getSelect()
            ->joinInner(
                ['order' => $resource->getTable('sales_order')],
                implode(' AND ', $orderJoinCondition),
                []
            )->where(
                'parent_item_id IS NULL'
            )->group(
                'order_items.product_id'
            )->order(
                'qty_ordered DESC'
            );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepares page sizes for dashboard grid with las 5 orders
     *
     * @return void
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize(5);
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'name',
            [
                'header' => __('Product'),
                'sortable' => false,
                'type' => 'text',
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'sortable' => false,
                'type' => 'currency',
                'currency_code' => (string)$this->_storeManager->getStore(
                    (int)$this->getParam('store')
                )->getBaseCurrencyCode(),
                'index' => 'price'
            ]
        );
        $this->addColumn(
            'qty_ordered',
            [
                'header' => __('Quantity'),
                'sortable' => false,
                'type' => 'number',
                'index' => 'qty_ordered'
            ]
        );
        
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('catalog/product/edit', ['id' => $row->getId()]);
    }
}
