<?php

namespace Vnecoms\VendorsApi\Model\Data\Sale;

use Magento\Framework\Model\AbstractModel;

/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class ItemQty extends AbstractModel implements
    \Vnecoms\VendorsApi\Api\Data\Sale\ItemQtyInterface
{
    /**
     * @return int
     */
    public function getItemId(){
        return $this->_getData(self::ITEM_ID);
    }

    /**
     * @return float
     */
    public function getQty(){
        return $this->_getData(self::QTY);
    }

    /**
     * @param int $id
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ItemQtyInterface
     */
    public function setItemId($id){
        return $this->setData(self::ITEM_ID, $id);
    }

    /**
     * @param float $qty
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\ItemQtyInterface
     */
    public function setQty($qty){
        return $this->setData(self::QTY, $qty);
    }

}


