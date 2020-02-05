<?php

namespace Vnecoms\VendorsApi\Api\Data\Dashboard;

interface DashboardInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const CREDIT_AMOUNT     = 'credit_amount';
    const LIFETIME_SALES    = 'lifetime_sales';
    const AVERAGE_ORDERS    = 'average_orders';
    const TOTAL_PRODUCTS    = 'total_products';
    const ORDER_CHART       = 'order_chart';
    const AMOUNT_CHART      = 'amount_chart';
    const CREDIT_CHART      = 'credit_chart';
    
    /**#@-*/
    
    /**
     * Get credit amount
     *
     * @return string|null
     */
    public function getCreditAmount();
    
    /**
     * Set credit amount
     *
     * @param string $amount
     * @return $this
     */
    public function setCreditAmount($amount);
    
    /**
     * Get lifetime sales
     * 
     * @return string
     */
    public function getLifetimeSales();
    
    /**
     * Set lifetime sales
     *
     * @param string $lifetimeSales
     * @return $this
     */
    public function setLifetimeSales($lifetimeSales);
    
    /**
     * Get average orders
     *
     * @return string
     */
    public function getAverageOrders();
    
    /**
     * Set average orders
     *
     * @param string $averageOrder
     * @return $this
     */
    public function setAverageOrders($averageOrder);

    /**
     * Get total products
     *
     * @return int
     */
    public function getTotalProducts();
    
    /**
     * Set total products
     *
     * @param string $totalProducts
     * @return $this
     */
    public function setTotalProducts($totalProducts);
    
    /**
     * Get order chart data
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Dashboard\OrderChartInterface[]
     */
    public function getOrderChartData();
    
    /**
     * Set order chart data
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Dashboard\OrderChartInterface[] $orderChartData
     * @return $this
     */
    public function setOrderChartData($orderChartData);
    
    /**
     * Get amount chart data
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Dashboard\OrderChartInterface[]
     */
    public function getAmountChartData();
    
    /**
     * Set amount chart data
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Dashboard\OrderChartInterface[] $amountrChartData
     * @return $this
     */
    public function setAmountChartData($amountrChartData);
    
    /**
     * Get credit chart data
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Dashboard\CreditChartInterface[]
     */
    public function getCreditChartData();
    
    /**
     * Set credit chart data
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Dashboard\CreditChartInterface[] $creditChartData
     * @return $this
     */
    public function setCreditChartData($creditChartData);
}
