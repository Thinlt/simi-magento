<?php

namespace Vnecoms\VendorsApi\Model\Data\Dashboard;


/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class CreditChart extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Vnecoms\VendorsApi\Api\Data\Dashboard\CreditChartInterface
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
     * Get received credit
     * 
     * @return float
     */
    public function getReceived(){
        return $this->_get(self::RECEIVED);
    }
    
    /**
     * Set received credit
     *
     * @param float $received
     * @return $this
     */
    public function setReceived($received){
        return $this->setData(self::RECEIVED, $received);
    }
    
    /**
     * Get spent credit
     *
     * @return float
     */
    public function getSpent(){
        return $this->_get(self::SPENT);
    }
    
    /**
     * Set spent credit
     *
     * @param float $spent
     * @return $this
     */
    public function setSpent($spent){
        return $this->setData(self::SPENT, $spent);
    }
}
