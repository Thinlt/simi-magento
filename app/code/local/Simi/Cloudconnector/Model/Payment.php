<?php

// app/code/local/Envato/Customshippingmethod/Model
class Simi_Cloudconnector_Model_Payment extends Mage_Payment_Model_Method_Abstract
{

    public function __Construct()
    {

    }

    protected $_code = 'simi_shipping';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $result = Mage::getModel('shipping/rate_result');
        $result->append($this->_getDefaultRate());

        return $result;
    }

    public function getAllowedMethods()
    {
        return array(
            'simi_shipping' => $this->getConfigData('name'),
        );
    }

    public function isAvailable($quote = null)
    {
        return true;
    }


    protected function _getDefaultRate()
    {
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod($this->_code);
        $rate->setMethodTitle($this->getConfigData('name'));
        $rate->setPrice($this->getConfigData('price'));
        $rate->setCost(0);

        return $rate;
    }
}