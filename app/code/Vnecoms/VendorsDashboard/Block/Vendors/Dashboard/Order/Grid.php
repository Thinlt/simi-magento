<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsDashboard\Block\Vendors\Dashboard\Order;

use Magento\Framework\Registry;
use Vnecoms\Vendors\Model\Session as VendorSession;

/**
 * Adminhtml seller dashboard recent transaction grid
 *
 */
class Grid extends \Vnecoms\VendorsDashboard\Block\Adminhtml\Dashboard\Order\Grid
{
    protected $_template = 'Vnecoms_VendorsDashboard::dashboard/grid.phtml';
    
    /**
     * @var VendorSession
     */
    protected $_vendorSession;
    
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
        VendorSession $vendorSession,
        Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $moduleManager,
            $collectionFactory,
            $statusOptions,
            $coreRegistry
        );
        $this->_vendorSession = $vendorSession;
    }
    
    /**
     * Get Vendor
     *
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor()
    {
        return $this->_vendorSession->getVendor();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $row->getId()]);
    }
}
