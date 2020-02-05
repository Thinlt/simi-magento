<?php

namespace Vnecoms\VendorsApi\Api\Data\Sale\Order;

interface ItemOptionInterface
{
    const LABEL = 'label';
    const VALUE = 'value';
    
    /**
     * Get Label
     *
     * @return string|null
     */
    public function getLabel();
    
    /**
     * Get Value
     *
     * @return string|null
     */
    public function getValue();
    
    /**
     * Get Label
     * 
     * @param string $label
     * @return ItemOptionInterface
     */
    public function setLabel($label);
    
    /**
     * Get Value
     * @param string $value
     * @return ItemOptionInterface
     */
    public function setValue($value);
}
