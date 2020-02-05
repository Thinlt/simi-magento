<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsDashboard\Block\Adminhtml\Dashboard\Transaction;

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
     * @var \Vnecoms\Credit\Model\ResourceModel\Credit\Transaction\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    
    /*
     * @var \Vnecoms\Credit\Model\Source\Transaction\Type
     */
    protected $_transactionType;

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
        \Vnecoms\Credit\Model\Source\Transaction\Type $transactionType,
        \Vnecoms\Credit\Model\ResourceModel\Credit\Transaction\CollectionFactory $collectionFactory,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_transactionType = $transactionType;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lastTransactionGrid');
    }

    /**
     * Prepare content for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabLabel()
    {
        return __('Last Transactions');
    }
    
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Last Transactions');
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
        $collection->setOrder('transaction_id', 'desc');
        $collection->addFieldToFilter('customer_id', $this->getVendor()->getCustomer()->getId());
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
            'created_at',
            [
                'header' => __('Created at'),
                'sortable' => false,
                'type' => 'date',
                'index' => 'created_at'
            ]
        );
        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'sortable' => false,
                'type' => 'options',
                'options' => $this->_transactionType->getOptionArray(),
                'index' => 'type'
            ]
        );
        
        $this->addColumn(
            'description',
            [
                'header' => __('Description'),
                'sortable' => false,
                'type' => 'text',
                'index' => 'description',
                'renderer' => 'Vnecoms\Vendors\Block\Widget\Grid\Column\Renderer\Html'
            ]
        );
        
        $baseCurrencyCode = $this->_storeManager->getStore(0)->getBaseCurrencyCode();

        $this->addColumn(
            'amount',
            [
                'header' => __('Amount'),
                'sortable' => false,
                'type' => 'currency',
                'currency_code' => $baseCurrencyCode,
                'index' => 'amount'
            ]
        );
        
        $this->addColumn(
            'balance',
            [
                'header' => __('Balance'),
                'sortable' => false,
                'type' => 'currency',
                'currency_code' => $baseCurrencyCode,
                'index' => 'balance'
            ]
        );
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }
}
