<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsDashboard\Block;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Adminhtml footer block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Dashboard extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;
    
    /**
     * @var \Vnecoms\Credit\Model\CreditFactory
     */
    protected $_creditFactory;
    
    /**
     * @var \Vnecoms\Credit\Model\Credit\TransactionFactory
     */
    protected $_transactionFactory;
    
    /**
     * @var \Vnecoms\VendorsSales\Model\OrderFactory
     */
    protected $_orderFactory;
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    
    /**
     * constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Vnecoms\Vendors\Model\Session $vendorSession
     * @param \Vnecoms\Credit\Model\CreditFactory $creditFactory
     * @param \Vnecoms\Credit\Model\Credit\TransactionFactory $transactionFactory
     * @param \Vnecoms\VendorsSales\Model\OrderFactory $orderFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Vendors\Model\Session $vendorSession,
        \Vnecoms\Credit\Model\CreditFactory $creditFactory,
        \Vnecoms\Credit\Model\Credit\TransactionFactory $transactionFactory,
        \Vnecoms\VendorsSales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_creditFactory = $creditFactory;
        $this->_priceCurrency = $priceCurrency;
        $this->_orderFactory = $orderFactory;
        $this->_productFactory = $productFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->_vendorSession = $vendorSession;
    }
    /**
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor()
    {
        return $this->_vendorSession->getVendor();
    }
    
    /**
     * Get get credit amount of current vendor
     * @return float
     */
    public function getCreditAmount()
    {
        if (!$this->getData('credit_amount')) {
            $creditAccount = $this->_creditFactory->create();
            $creditAccount->loadByCustomerId($this->getVendor()->getCustomer()->getId());
    
            $this->setData('credit_amount', $creditAccount->getCredit());
        }
        return $this->getData('credit_amount');
    }
    
    /**
     * Get lifetime sales
     * @return float
     */
    public function getLifetimeSales()
    {
        return $this->_orderFactory->create()->getResource()->getLifetimeSales($this->getVendor()->getId());
    }
    
    /**
     * Get Average orders
     * @return float
     */
    public function getAverageOrders()
    {
        return $this->_orderFactory->create()->getResource()->getAverageOrders($this->getVendor()->getId());
    }
    
    /**
     * Get number of products of current vendor
     *
     * @return int
     */
    public function getTotalProducts()
    {
        $resource = $this->_productFactory->create()->getResource();
    
        $connection = $resource->getConnection();
        $select = $connection->select();
        $select->from(
            $resource->getTable('catalog_product_entity'),
            ['total_product' => 'count( entity_id )']
        )->where(
            'vendor_id = :vendor_id'
        );
        $bind = ['vendor_id' => $this->getVendor()->getId()];
    
        $total = $connection->fetchOne($select, $bind);
        return $total;
    }

    /**
     * Get not shiped orders of current vendor
     * @return int
     */
    public function getNotShippedOrders(){

        $resource = $this->_productFactory->create()->getResource();
        $connection = $resource->getConnection();
        $select = $connection->select();
        $select->from(
            ["main_table"=>$resource->getTable('ves_vendor_sales_order')],
            ['*']
        )->join(
            [
                'order_grid'=>$resource->getTable('sales_order')],
            'main_table.order_id = order_grid.entity_id',
            [
                'is_virtual'
            ]
        )->join(
            [
                'order_item'=>$resource->getTable('sales_order_item')],
            'main_table.entity_id=order_item.vendor_order_id',
            [
                'is_virtual',
                'locked_do_ship',
                'parent_item_id',
            ]
        )->where(
            'main_table.vendor_id = :vendor_id'
        )->where(
            'order_grid.is_virtual = 0'
        )->where(
            'order_item.locked_do_ship IS NULL'
        )->where(
            'order_item.is_virtual = 0'
        )
            ->where(
                'order_item.parent_item_id IS NULL'
            )->where(
                '(order_item.qty_ordered - order_item.qty_shipped - order_item.qty_refunded  - order_item.qty_canceled) >= 1'
            )->group('main_table.order_id');
        $bind = ['vendor_id' => $this->getVendor()->getId()];

        $total = $connection->fetchALL($select,$bind);

        return count($total);
    }

    /**
     * Format Price currency
     * @param float $amount
     * @return string
     */
    public function formatPrice($amount)
    {
        return $this->_storeManager->getStore()->getBaseCurrency()->formatPrecision($amount, 2, [], false);
    }
    
    /**
     * Get graph url
     * @return string
     */
    public function getGraphUrl()
    {
        return $this->getUrl('dashboard/graph');
    }
}
