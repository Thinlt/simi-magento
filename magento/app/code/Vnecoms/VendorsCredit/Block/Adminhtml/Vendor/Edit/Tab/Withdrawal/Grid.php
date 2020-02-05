<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Block\Adminhtml\Vendor\Edit\Tab\Withdrawal;

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
    
    /*
     * @var \Vnecoms\VendorsCredit\Model\Source\Status
     */
    protected $_withdrawalStatus;

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
        \Vnecoms\VendorsCredit\Model\Source\Status $withdrawalStatus,
        \Vnecoms\VendorsCredit\Model\ResourceModel\Withdrawal\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_withdrawalStatus = $withdrawalStatus;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('withdrawalRequestsGrid');
        $this->setDefaultLimit(20);
        $this->setPagerVisibility(true);
        $this->setFilterVisibility(true);
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
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
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created at'),
                'sortable' => true,
                'type' => 'date',
                'index' => 'created_at'
            ]
        );
        $this->addColumn(
            'method_title',
            [
                'header' => __('Method'),
                'sortable' => true,
                'type' => 'text',
                'index' => 'method_title'
            ]
        );
        
        $baseCurrencyCode = $this->_storeManager->getStore(0)->getBaseCurrencyCode();

        $this->addColumn(
            'amount',
            [
                'header' => __('Amount'),
                'sortable' => true,
                'type' => 'currency',
                'currency_code' => $baseCurrencyCode,
                'index' => 'amount'
            ]
        );
        
        $this->addColumn(
            'fee',
            [
                'header' => __('Fee'),
                'sortable' => true,
                'type' => 'currency',
                'currency_code' => $baseCurrencyCode,
                'index' => 'fee'
            ]
        );
        
        $this->addColumn(
            'net_amount',
            [
                'header' => __('Net Amount'),
                'sortable' => true,
                'type' => 'currency',
                'currency_code' => $baseCurrencyCode,
                'index' => 'net_amount'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'sortable' => true,
                'type' => 'options',
                'index' => 'status',
                'options' => $this->_withdrawalStatus->getOptionArray(),
                'renderer' => 'Vnecoms\VendorsCredit\Block\Adminhtml\Vendor\Edit\Tab\Withdrawal\Status',
            ]
        );
        

        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('vendors/credit_withdrawal/grid', ['_current'=>true]);
    }
}
