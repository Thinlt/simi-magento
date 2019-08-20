<?php

namespace Vnecoms\VendorsApi\Model\Data\Sale;

use Magento\Framework\Model\AbstractModel;

/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Tracking extends AbstractModel implements
    \Vnecoms\VendorsApi\Api\Data\Sale\TrackingInterface
{
    /**
     * @return string
     */
    public function getCarrierCode(){
        return $this->_getData(self::CARRIER_CODE);
    }

    /**
     * @return string
     */
    public function getTitle(){
        return $this->_getData(self::TITLE);
    }

    /**
     * @return float
     */
    public function getNumber(){
        return $this->_getData(self::NUMBER);
    }

    /**
     * @param string $carrierCode
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\TrackingInterface
     */
    public function setCarrierCode($carrierCode){
        return $this->setData(self::CARRIER_CODE, $carrierCode);
    }

    /**
     * @param string $title
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\TrackingInterface
     */
    public function setTitle($title){
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @param float $number
     * @return \Vnecoms\VendorsApi\Api\Data\Sale\TrackingInterface
     */
    public function setNumber($number){
        return $this->setData(self::NUMBER, $number);
    }
}
