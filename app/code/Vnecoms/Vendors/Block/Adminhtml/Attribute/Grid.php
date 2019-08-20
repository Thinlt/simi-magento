<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Product attributes grid
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Vnecoms\Vendors\Block\Adminhtml\Attribute;

use Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends AbstractGrid
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Vnecoms\Vendors\Model\ResourceModel\Attribute\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_module = 'vendors';
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Prepare product attributes grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $collection->addVisibleFilter();
        //$collection->addFieldToFilter('attribute_code',array('like'=>'ves_vendor_%'));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare product attributes grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter(
            'backend_type',
            [
                'header' => __('Type'),
                'sortable' => true,
                'index' => 'backend_type',
                'type' => 'text',
            ],
            'frontend_label'
        );

        $this->addColumnAfter(
            'frontend_input',
            [
                'header' => __('Frontend Input'),
                'sortable' => true,
                'index' => 'frontend_input',
                'type' => 'text',
            ],
            'backend_type'
        );

        return $this;
    }
}
