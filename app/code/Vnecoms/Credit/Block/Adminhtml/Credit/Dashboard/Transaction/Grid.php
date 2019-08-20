<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Adminhtml\Credit\Dashboard\Transaction;

/**
 * Adminhtml dashboard recent orders grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends \Magento\Backend\Block\Dashboard\Grid
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
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_transactionType = $transactionType;
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
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $collection->setOrder('transaction_id','desc');
        $collection->join($collection->getTable('customer_grid_flat'), "entity_id=customer_id",['email'=>'email','name'=>'name'],null,'left');
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
        // Remove count of total orders
        // $this->getCollection()->setCurPage($this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        /* $this->addColumn(
            'transaction_id',
            [
                'header' => __('Transaction Id'),
                'type' => 'number',
                'sortable' => false,
                'index' => 'transaction_id'
            ]
        ); */
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
            'name',
            [
                'header' => __('Name'),
                'sortable' => false,
                'type' => 'text',
                'index' => 'name'
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
                'renderer' => 'Vnecoms\Credit\Block\Adminhtml\Credit\Dashboard\Transaction\Grid\Renderer\Description'
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
