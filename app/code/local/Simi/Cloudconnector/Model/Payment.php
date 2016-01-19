<?php

// app/code/local/Envato/Customshippingmethod/Model
class Simi_Cloudconnector_Model_Payment extends Mage_Payment_Model_Method_Abstract

{
    protected $_code = 'simi_payment';
//    protected $_formBlockType = 'simi_payment/form_custompaymentmethod';
//    protected $_infoBlockType = 'simi_payment/info_custompaymentmethod';

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

//    public function validate()
//    {
//        parent::validate();
//        $info = $this->getInfoInstance();
//
//        if (!$info->getCustomFieldOne()) {
//            $errorCode = 'invalid_data';
//            $errorMsg = $this->_getHelper()->__("CustomFieldOne is a required field.\n");
//        }
//
//        if (!$info->getCustomFieldTwo()) {
//            $errorCode = 'invalid_data';
//            $errorMsg .= $this->_getHelper()->__('CustomFieldTwo is a required field.');
//        }
//
//        if ($errorMsg) {
//            Mage::throwException($errorMsg);
//        }
//
//        return $this;
//    }

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('simi_payment/payment/redirect', array('_secure' => false));
    }

}