<?php

namespace Vnecoms\VendorsApi\Model\Data\Dashboard;


/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Dashboard extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Vnecoms\VendorsApi\Api\Data\Dashboard\DashboardInterface
{
    /**
     * Get credit amount
     *
     * @return string|null
     */
    public function getCreditAmount()
    {
        return $this->_get(self::CREDIT_AMOUNT);
    }
    
    /**
     * Set credit amount
     *
     * @param string $amount
     * @return $this
     */
    public function setCreditAmount($amount)
    {
        return $this->setData(self::CREDIT_AMOUNT, $amount);
    }
    
    /**
     * Get lifetime sales
     *
     * @return string
     */
    public function getLifetimeSales()
    {
        return $this->_get(self::LIFETIME_SALES);
    }
    
    /**
     * Set lifetime sales
     *
     * @param string $lifetimeSales
     * @return $this
     */
    public function setLifetimeSales($lifetimeSales)
    {
        return $this->setData(self::LIFETIME_SALES, $lifetimeSales);
    }
    
    /**
     * Get average orders
     *
     * @return string
     */
    public function getAverageOrders()
    {
        return $this->_get(self::AVERAGE_ORDERS);
    }
    
    /**
     * Set average orders
     *
     * @param string $averageOrder
     * @return $this
     */
    public function setAverageOrders($averageOrder)
    {
        return $this->setData(self::AVERAGE_ORDERS, $averageOrder);
    }
    
    /**
     * Get total products
     *
     * @return int
     */
    public function getTotalProducts()
    {
        return $this->_get(self::TOTAL_PRODUCTS);
    }
    
    /**
     * Set total products
     *
     * @param string $totalProducts
     * @return $this
     */
    public function setTotalProducts($totalProducts)
    {
        return $this->setData(self::TOTAL_PRODUCTS, $totalProducts);
    }
    
    /**
     * Get order chart data
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Dashboard\OrderChartInterface[]
     */
    public function getOrderChartData()
    {
        return $this->_get(self::ORDER_CHART);
    }
    
    /**
     * Set order chart data
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Dashboard\OrderChartInterface[] $orderChartData
     * @return $this
     */
    public function setOrderChartData($orderChartData)
    {
        return $this->setData(self::ORDER_CHART, $orderChartData);
    }
    
    /**
     * Get amount chart data
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Dashboard\OrderChartInterface[]
     */
    public function getAmountChartData()
    {
        return $this->_get(self::AMOUNT_CHART);
    }
    
    /**
     * Set amount chart data
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Dashboard\OrderChartInterface[] $amountrChartData
     * @return $this
     */
    public function setAmountChartData($amountrChartData)
    {
        return $this->setData(self::AMOUNT_CHART, $amountrChartData);
    }
    
    /**
     * Get credit chart data
     *
     * @return \Vnecoms\VendorsApi\Api\Data\Dashboard\CreditChartInterface[]
     */
    public function getCreditChartData()
    {
        return $this->_get(self::CREDIT_CHART);
    }
    
    /**
     * Set credit chart data
     *
     * @param \Vnecoms\VendorsApi\Api\Data\Dashboard\CreditChartInterface[] $creditChartData
     * @return $this
     */
    public function setCreditChartData($creditChartData)
    {
        return $this->setData(self::CREDIT_CHART, $creditChartData);
    }
}
