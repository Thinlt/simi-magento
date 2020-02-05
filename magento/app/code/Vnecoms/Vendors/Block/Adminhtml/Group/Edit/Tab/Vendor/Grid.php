<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Adminhtml\Group\Edit\Tab\Vendor;

use Vnecoms\Vendors\Model\ResourceModel\Vendor\CollectionFactory;

/**
 * Customer Credit transactions grid
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends \Magento\Backend\Block\Dashboard\Grid
{
    protected $_template = 'Magento_Backend::widget/grid.phtml';
    
    /**
     * @var \Vnecoms\Vendors\Model\ResourceModel\Vendors\CollectionFactory
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
     * @var \Vnecoms\Vendors\Model\Source\Status
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
        CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        \Vnecoms\Vendors\Model\Source\Status $statusOptions,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_coreRegistry = $registry;
        $this->_statusOptions = $statusOptions;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sellerGrid');
        $this->setDefaultLimit(20);
        $this->setPagerVisibility(true);
        $this->setFilterVisibility(true);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $collection->setOrder('entity_id', 'desc');
        $collection->addAttributeToFilter('group_id', $this->getGroup()->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * Get current group
     *
     * @return \Vnecoms\Vendors\Model\Group
     */
    public function getGroup()
    {
        return $this->_coreRegistry->registry('current_group');
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
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'type' => 'number',
                'index' => 'entity_id'
            ]
        );
        $this->addColumn(
            'vendor_id',
            [
                'header' => __('Vendor Id'),
                'sortable' => true,
                'type' => 'text',
                'index' => 'vendor_id'
            ]
        );
        
        $this->addColumn(
            'firstname',
            [
                'header' => __('First name'),
                'sortable' => true,
                'type' => 'text',
                'index' => 'firstname'
            ]
        );
        $this->addColumn(
            'lastname',
            [
                'header' => __('Last name'),
                'sortable' => true,
                'type' => 'text',
                'index' => 'lastname'
            ]
        );
        
        $this->addColumn(
            'telephone',
            [
                'header' => __('Phone'),
                'sortable' => true,
                'type' => 'text',
                'index' => 'telephone'
            ]
        );
        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'sortable' => true,
                'type' => 'text',
                'index' => 'email'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'sortable' => true,
                'type' => 'options',
                'index' => 'status',
                'options' => $this->_statusOptions->getOptionArray(),
            ]
        );

        return parent::_prepareColumns();
    }
    
    /**
     * Get Grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('vendors/group/vendorGrid', ['_current'=>true]);
    }
}
