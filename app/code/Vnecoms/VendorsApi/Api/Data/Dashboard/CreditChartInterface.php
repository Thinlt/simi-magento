<?php

namespace Vnecoms\VendorsApi\Api\Data\Dashboard;

interface CreditChartInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const TIME              = 'time';
    const RECEIVED          = 'received';
    const SPENT             = 'spent';
    
    
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
     * Get received credit
     * 
     * @return float
     */
    public function getReceived();
    
    /**
     * Set received credit
     *
     * @param float $received
     * @return $this
     */
    public function setReceived($received);
    
    /**
     * Get spent credit
     *
     * @return float
     */
    public function getSpent();
    
    /**
     * Set spent credit
     *
     * @param float $spent
     * @return $this
     */
    public function setSpent($spent);
}
