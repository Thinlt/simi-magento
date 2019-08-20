<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Adminhtml\Customer\Edit\Tab\Credit\Transaction;

use Magento\Customer\Controller\RegistryConstants;

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
     * @var \Vnecoms\Credit\Model\Source\Transaction\Type
     */
    protected $_transactionType;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
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
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_transactionType = $transactionType;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lastCreditTransactionGrid');
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
        $collection->addFieldToFilter('customer_id',$this->getCustomerId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
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
                'sortable' => true,
                'index' => 'transaction_id'
            ]
        ); */
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
            'type',
            [
                'header' => __('Type'),
                'sortable' => true,
                'type' => 'options',
                'options' => $this->_transactionType->getOptionArray(),
                'index' => 'type'
            ]
        );
        
        $this->addColumn(
            'description',
            [
                'header' => __('Description'),
                'sortable' => true,
                'type' => 'text',
                'index' => 'description'
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
            'balance',
            [
                'header' => __('Balance'),
                'sortable' => true,
                'type' => 'currency',
                'currency_code' => $baseCurrencyCode,
                'index' => 'balance'
            ]
        );
        

        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('storecredit/account/grid', array('_current'=>true));
    }

}
