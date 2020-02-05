<?php

namespace Vnecoms\VendorsApi\Api\Data\Dashboard;

interface OrderChartInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const TIME              = 'time';
    const NUMBER_OF_ORDER   = 'number_of_order';
    const ORDER_AMOUNT      = 'order_amount';
    
    
    /**#@-*/
    
    /**
     * Get time
     *
     * @return string
     */
    public function getTime();
    
    /**
     * Set time
     *
     * @param string $time
     * @return $this
     */
    public function setTime($time);
    
    /**
     * Get number of order
     * 
     * @return int
     */
    public function getNumberOfOrder();
    
    /**
     * Set number of order
     *
     * @param int $numberOfOrder
     * @return $this
     */
    public function setNumberOfOrder($numberOfOrder);
    
    /**
     * Get order amount
     *
     * @return float
     */
    public function getOrderAmount();
    
    /**
     * Set order amount
     *
     * @param float $orderAmount
     * @return $this
     */
    public function setOrderAmount($orderAmount);
}
