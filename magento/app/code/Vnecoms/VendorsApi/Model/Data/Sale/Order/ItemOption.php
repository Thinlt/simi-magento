<?php

namespace Vnecoms\VendorsApi\Model\Data\Sale\Order;

/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class ItemOption extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Vnecoms\VendorsApi\Api\Data\Sale\Order\ItemOptionInterface
{
    /**
     * @see \Vnecoms\VendorsApi\Api\Data\Sale\Order\ItemOptionInterface::getLabel()
     */
    public function getLabel(){
        return $this->_get(self::LABEL);
    }

    /**
     * @see \Vnecoms\VendorsApi\Api\Data\Sale\Order\ItemOptionInterface::getValue()
     */
    public function getValue(){
        return $this->_get(self::VALUE);
    }
    
    /**
     * @see \Vnecoms\VendorsApi\Api\Data\Sale\Order\ItemOptionInterface::setLabel()
     */
    public function setLabel($label){
        return $this->setData(self::LABEL, $label);
    }

    /**
     * @see \Vnecoms\VendorsApi\Api\Data\Sale\Order\ItemOptionInterface::setValue()
     */
    public function setValue($value){
        return $this->setData(self::VALUE, $value);
    }

}
