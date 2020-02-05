<?php
/**
 * @category    Magento
 * @package     Magento_Sales
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order\View;

/**
 * Adminhtml sales order view
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class History extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Sales data
     *
     * @var \Magento\Sales\Helper\Data
     */
    protected $_salesData = null;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\History\Collection
     */
    protected $_statusHistory;
    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;
    /**
     * @var \Magento\Sales\Helper\Admin
     */
    private $adminHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_salesData = $salesData;
        $this->_orderConfig = $orderConfig;
        $this->_statusHistory = $collectionFactory->create();
        parent::__construct($context, $data);
        $this->adminHelper = $adminHelper;
    }
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('order_history_block').parentNode, '" . $this->getSubmitUrl() . "')";
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Submit Comment'), 'class' => 'action-save action-secondary', 'onclick' => $onclick]
        );
        $this->setChild('submit_button', $button);
        return parent::_prepareLayout();
    }
    /**
     * Submit URL getter
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        /*$vendorId = $this->getRequest()->getParam('vendor_id');
        $vendorOrderId = $this->getRequest()->getParam('vendor_order_id');
        $params = $this->getRequest()->getParams();
        $params = array_merge($params,[
            'vendor_order_id'=>$vendorOrderId,
            'vendor_id' => $vendorId
        ]);*/
        return $this->getUrl('sales/*/addComment', ['order_id' => $this->getVendorOrder()->getId()]);
    }

    /**
     * Retrieve order model
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getVendorOrder()
    {
        return $this->_coreRegistry->registry('vendor_order');
    }

    public function getStatuses()
    {
        $status = $this->getVendorOrder()->getStatus();
         ($status == 'pending')? $status ='new':$status;
        $statuses = $this->getConfig()->getStateStatuses($status);
        return $statuses;
    }


    public function getStatusHistoryCollection()
    {
        $vendorId = $this->getRequest()->getParam('vendor_id');
        $vendorOrderId = $this->getRequest()->getParam('vendor_order_id');
        $statusHistory = $this->_statusHistory->addFieldToFilter('vendor_id', $vendorId)->addFieldToFilter('vendor_order_status', $vendorOrderId);
        return $statusHistory;
    }
    /**
     * Retrieve order configuration model
     *
     * @return \Magento\Sales\Model\Order\Config
     */
    public function getConfig()
    {
        return $this->_orderConfig;
    }
    /**
     * Retrieve label of order status
     *
     * @return string
     */
    public function getStatusLabel($status)
    {
        return $this->getConfig()->getStatusLabel($status);
    }
    /**
     * Check allow to send order comment email
     *
     * @return bool
     */
    public function canSendCommentEmail()
    {
        return $this->_salesData->canSendOrderCommentEmail($this->getVendorOrder()->getOrder()->getStore()->getId());
    }
    /**
     * Customer Notification Applicable check method
     *
     * @param  \Magento\Sales\Model\Order\Status\History $history
     * @return bool
     */
    public function isCustomerNotificationNotApplicable(\Magento\Sales\Model\Order\Status\History $history)
    {
        return $history->isCustomerNotificationNotApplicable();
    }
    /**
     * Replace links in string
     *
     * @param array|string $data
     * @param null|array $allowedTags
     * @return string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->adminHelper->escapeHtmlWithLinks($data, $allowedTags);
    }

    /**
     * Get order status label
     * @return string
     */
    public function getOrderStatusLabel()
    {
        return $this->getConfig()->getStatusLabel($this->getVendorOrder()->getStatus());
    }
}
