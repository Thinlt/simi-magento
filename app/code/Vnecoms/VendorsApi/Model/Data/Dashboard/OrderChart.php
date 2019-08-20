<?php

namespace Vnecoms\VendorsApi\Model\Data\Dashboard;


/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class OrderChart extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Vnecoms\VendorsApi\Api\Data\Dashboard\OrderChartInterface
{
    /**#@-*/
    
    /**
     * Get time
     *
     * @return string
     */
    public function getTime(){
        return $this->_get(self::TIME);
    }
    
    /**
     * Set time
     *
     * @param string $time
     * @return $this
     */
    public function setTime($time){
        return $this->setData(self::TIME, $time);
    }
    
    /**
     * Get number of order
     *
     * @return int
     */
    public function getNumberOfOrder(){
        return $this->_get(self::NUMBER_OF_ORDER);
    }
    
    /**
     * Set number of order
     *
     * @param int $numberOfOrder
     * @return $this
     */
    public function setNumberOfOrder($numberOfOrder){
        return $this->setData(self::NUMBER_OF_ORDER, $numberOfOrder);
    }
    
    /**
     * Get order amount
     *
     * @return float
     */
    public function getOrderAmount(){
        return $this->_get(self::ORDER_AMOUNT);
    }
    
    /**
     * Set order amount
     *
     * @param float $orderAmount
     * @return $this
     */
    public function setOrderAmount($orderAmount){
        return $this->setData(self::ORDER_AMOUNT, $orderAmount);
    }
}
