<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Adminhtml\Vendor\Edit\Tab\Order;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Adminhtml customer orders grid block
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Sales reorder
     *
     * @var \Magento\Sales\Helper\Reorder
     */
    protected $_salesReorder = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory
     * @param \Magento\Sales\Helper\Reorder $salesReorder
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Vnecoms\VendorsSales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('vendor_orders_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $collection->join(
            ['order_grid' => $collection->getTable('sales_order_grid')],
            'main_table.order_id=order_grid.entity_id',
            [
                'increment_id',
                'store_id',
                'store_name',
                'base_currency_code',
                'order_currency_code',
                'shipping_name',
                'billing_name',
                'billing_address',
                'shipping_address',
                'shipping_and_handling',
                'total_refunded',
                'customer_name'
            ]
        );
            $collection->addFieldToFilter('vendor_id', $this->getVendor()->getId());
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
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', ['header' => __('Order'), 'width' => '100', 'index' => 'increment_id']);

        $this->addColumn(
            'created_at',
            ['header' => __('Purchased'), 'index' => 'created_at', 'type' => 'datetime']
        );

        $this->addColumn('billing_name', ['header' => __('Bill-to Name'), 'index' => 'billing_name']);

        $this->addColumn('shipping_name', ['header' => __('Ship-to Name'), 'index' => 'shipping_name']);

        $this->addColumn(
            'grand_total',
            [
                'header' => __('Order Total'),
                'index' => 'grand_total',
                'type' => 'currency',
                'currency' => 'order_currency_code'
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                ['header' => __('Purchase Point'), 'index' => 'store_id', 'type' => 'store', 'store_view' => true]
            );
        }

        return parent::_prepareColumns();
    }

    /**
     * Retrieve the Url for a specified sales order row.
     *
     * @param \Magento\Sales\Model\Order|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $row->getOrderId()]);
    }
    

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        return $this->getUrl('vendors/sales_order/grid', ['_current' => true]);
    }
}
