<?php

namespace Vnecoms\VendorsApi\Model;

use Magento\Framework\App\ObjectManager;

/**
 * Vendor repository.
 */
class DashboardRepository implements \Vnecoms\VendorsApi\Api\DashboardRepositoryInterface
{
    /**
     * @var \Vnecoms\VendorsApi\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;
    
    /**
     * @var \Vnecoms\VendorsDashboard\Model\Graph
     */
    protected $graph;

    /**
     * DashboardRepository constructor.
     * @param \Vnecoms\VendorsApi\Helper\Data $helper
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Vnecoms\VendorsDashboard\Model\Graph $graph
     */
    public function __construct(
        \Vnecoms\VendorsApi\Helper\Data $helper,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Vnecoms\VendorsDashboard\Model\Graph $graph
    ) {
        $this->helper               = $helper;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->graph                = $graph;
    }
    
    /**
     * Format Price currency
     * @param float $amount
     * @return string
     */
    protected function formatPrice($amount)
    {
        $storeManage = ObjectManager::getInstance()->get('Magento\Store\Model\StoreManagerInterface');
        return $storeManage->getStore()->getBaseCurrency()->formatPrecision($amount, 2, [], false);
    }
    
    /**
     * Get customer credit amount
     * @param int $customerId
     * @return float
     */
    protected function getCreditAmount($customerId){
        $creditAccount = ObjectManager::getInstance()->create('Vnecoms\Credit\Model\Credit');
        $creditAccount->loadByCustomerId($customerId);
        return $creditAccount->getCredit();
    }

    /**
     * Get lifetime sales
     * 
     * @param int $vendorId
     * @return float
     */
    public function getLifetimeSales($vendorId)
    {
        $orderResource = ObjectManager::getInstance()->get('Vnecoms\VendorsSales\Model\ResourceModel\Order');
        return $orderResource->getLifetimeSales($vendorId);
    }
    
    /**
     * Get Average orders
     * 
     * @param int $vendorId
     * @return float
     */
    public function getAverageOrders($vendorId)
    {
        $orderResource = ObjectManager::getInstance()->get('Vnecoms\VendorsSales\Model\ResourceModel\Order');
        return $orderResource->getAverageOrders($vendorId);
    }
    
    /**
     * Get number of products of current vendor
     * 
     * @param int $vendorId
     * @return int
     */
    public function getTotalProducts($vendorId)
    {
        $resource = ObjectManager::getInstance()->create('Magento\Catalog\Model\ResourceModel\Product');
        
        $connection = $resource->getConnection();
        $select = $connection->select();
        $select->from(
            $resource->getTable('catalog_product_entity'),
            ['total_product' => 'count( entity_id )']
        )->where(
            'vendor_id = :vendor_id'
        );
        $bind = ['vendor_id' => $vendorId];
            
        $total = $connection->fetchOne($select, $bind);
        return $total;
    }
    
    /**
     * @param array $data
     * @return \Vnecoms\VendorsApi\Api\Data\Dashboard\CreditChartInterface[]
     */
    protected function prepareCreditChart($data){
        $om = ObjectManager::getInstance();
        $result = [];
        foreach($data as $creditData){
            $creditChart = $om->create('Vnecoms\VendorsApi\Api\Data\Dashboard\CreditChartInterface');
            $creditChart->setTime($creditData['y']);
            $creditChart->setReceived($creditData['received']);
            $creditChart->setSpent($creditData['spent']);
            $result[] = $creditChart;
        }
        
        return $result;
    }
    
    /**
     * @param array $data
     * @return \Vnecoms\VendorsApi\Api\Data\Dashboard\OrderChartInterface[]
     */
    protected function prepareOrderChart($data){
        $om = ObjectManager::getInstance();
        $result = [];
        foreach($data as $creditData){
            $orderChart = $om->create('Vnecoms\VendorsApi\Api\Data\Dashboard\OrderChartInterface');
            $orderChart->setTime($creditData['y']);
            $orderChart->setNumberOfOrder($creditData['order_num']);
            $orderChart->setOrderAmount($creditData['amount']);
            $result[] = $orderChart;
        }
        
        return $result;
    }
    
    /**
     * {@inheritDoc}
     * @see \Vnecoms\VendorsApi\Api\VendorRepositoryInterface::getDashboardInfo()
     */
    public function getDashboardInfo($customerId, $period = '7d'){
        $om = ObjectManager::getInstance();
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        
        $dashboardData = $om->get('Vnecoms\VendorsApi\Api\Data\Dashboard\DashboardInterface');
        $dashboardData->setCreditAmount($this->formatPrice($this->getCreditAmount($customerId)));
        $dashboardData->setLifetimeSales($this->formatPrice($this->getLifetimeSales($vendorId)));
        $dashboardData->setAverageOrders($this->formatPrice($this->getAverageOrders($vendorId)));
        $dashboardData->setTotalProducts($this->getTotalProducts($vendorId));
        $graph = $om->create('Vnecoms\VendorsDashboard\Model\Graph');
        
        switch ($period) {
            case '2y':
                $creditChartData    = $this->prepareCreditChart($graph->getTransactionDataLast2Years($customerId));
                $orderChartData     = $this->prepareOrderChart($graph->getOrdersDataLast2Years($vendorId));
                $amountChartData    = $this->prepareOrderChart($graph->getAmountsDataLast2Years($vendorId));
                break;
            case '1y':
                $creditChartData    = $this->prepareCreditChart($graph->getTransactionDataLastYear($customerId));
                $orderChartData     = $this->prepareOrderChart($graph->getOrdersDataLastYear($vendorId));
                $amountChartData    = $this->prepareOrderChart($graph->getAmountsDataLastYear($vendorId));
                break;
            case '1m':
                $creditChartData    = $this->prepareCreditChart($graph->getTransactionDataLastMonth($customerId));
                $orderChartData     = $this->prepareOrderChart($graph->getOrdersDataLastMonth($vendorId));
                $amountChartData    = $this->prepareOrderChart($graph->getAmountsDataLastMonth($vendorId));
                break;
            case '7d':
                $creditChartData    = $this->prepareCreditChart($graph->getTransactionDataLast7Days($customerId));
                $orderChartData     = $this->prepareOrderChart($graph->getOrdersDataLast7Days($vendorId));
                $amountChartData    = $this->prepareOrderChart($graph->getAmountsDataLast7Days($vendorId));
                break;
            case '24h':
            default:
                $creditChartData    = $this->prepareCreditChart($graph->getTransactionsDataLast24Hours($customerId));
                $orderChartData     = $this->prepareOrderChart($graph->getOrdersDataLast24Hours($vendorId));
                $amountChartData    = $this->prepareOrderChart($graph->getAmountsDataLast24Hours($vendorId));
        }
        
        $dashboardData->setOrderChartData($orderChartData);
        $dashboardData->setAmountChartData($amountChartData);
        $dashboardData->setCreditChartData($creditChartData);
        
        return $dashboardData;
    }
    
}
