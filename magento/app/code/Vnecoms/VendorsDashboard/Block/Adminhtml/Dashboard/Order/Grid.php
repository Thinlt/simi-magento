<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsDashboard\Block\Adminhtml\Dashboard\Order;

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
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Sales\Ui\Component\Listing\Column\Status\Options
     */
    protected $_statusOptions;
    
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
        \Magento\Framework\Module\Manager $moduleManager,
        \Vnecoms\VendorsSales\Model\ResourceModel\Order\Grid\CollectionFactory $collectionFactory,
        \Magento\Sales\Ui\Component\Listing\Column\Status\Options $statusOptions,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_collectionFactory = $collectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->_statusOptions = $statusOptions;
        
        parent::__construct($context, $backendHelper, $data);
    }

    
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lastOrdersGrid');
    }

    /**
     * Prepare content for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabLabel()
    {
        return __('Last Orders');
    }
    
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Last Orders');
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
        $collection->setOrder('entity_id', 'desc');
        $collection->addFieldToFilter('vendor_id', $this->getVendor()->getId());
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
            'increment_id',
            [
                'header' => __('ID'),
                'sortable' => false,
                'type' => 'text',
                'index' => 'increment_id'
            ]
        );
        $this->addColumn(
            'created_at',
            [
                'header' => __('Purchased Date'),
                'sortable' => false,
                'type' => 'date',
                'index' => 'created_at'
            ]
        );
        $this->addColumn(
            'customer_name',
            [
                'header' => __('Customer'),
                'sortable' => false,
                'type' => 'text',
                'index' => 'customer_name'
            ]
        );
        
        $baseCurrencyCode = $this->_storeManager->getStore(0)->getBaseCurrencyCode();

        $this->addColumn(
            'grand_total',
            [
                'header' => __('Grand Total'),
                'sortable' => false,
                'type' => 'currency',
                'currency_code' => $baseCurrencyCode,
                'index' => 'base_grand_total'
            ]
        );

        $options = [];
        foreach ($this->_statusOptions->toOptionArray() as $option) {
            $options[$option['value']] = $option['label'];
        }
        
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'sortable' => false,
                'type' => 'options',
                'options' => $options,
                'index' => 'status'
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
        return $this->getUrl('sales/order/view', ['order_id' => $row->getOrderId()]);
    }
}
