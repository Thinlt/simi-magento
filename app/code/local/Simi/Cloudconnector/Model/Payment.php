<?php

// app/code/local/Envato/Customshippingmethod/Model
class Simi_Cloudconnector_Model_Payment extends Mage_Payment_Model_Method_Abstract

{
    protected $_code = 'simi_payment';
    protected $_isAvailable = false;
    protected $_formBlockType = 'cloudconnector/custompaymentmethod';
   // protected $_infoBlockType = 'Cloudconnector/Custompaymentmethod';

    public function assignData($data)
    {
        $info = $this->getInfoInstance();

        if ($data->getCustomFieldOne()) {
            $info->setCustomFieldOne($data->getCustomFieldOne());
        }

        if ($data->getCustomFieldTwo()) {
            $info->setCustomFieldTwo($data->getCustomFieldTwo());
        }

        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('simi_payment/payment/redirect', array('_secure' => false));
    }

}