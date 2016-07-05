<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 5/3/16
 * Time: 9:37 PM
 */
class Simi_Simirewardpoints_Model_Api_Simirewardpoints extends Simi_Simiconnector_Model_Api_Abstract {

    protected $_DEFAULT_ORDER = 'customer_id';

    public function setBuilderQuery() {
        $data = $this->getData();
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($data['resourceid']) {
                $this->builderQuery = Mage::getModel('simirewardpoints/customer')->getCollection()->addFieldToFilter('customer_id', $customer->getId())->getFirstItem();
            } else {
                $this->builderQuery = Mage::getModel('simirewardpoints/customer')->getCollection()->addFieldToFilter('customer_id', $customer->getId());
            }
        } else
            throw new Exception(Mage::helper('customer')->__('Please login First.'), 4);
    }

    /*
     * Return Reward Points Information
     */

    public function show() {
        $data = $this->getData();
        $result = parent::show();
        if ($data['resourceid'] == 'home') {
            $return = Mage::getModel('simirewardpoints/simiappmapping')->getRewardInfo();
            $result['simirewardpoint'] = array_merge($result['simirewardpoint'], $return);
        } else if ($data['resourceid'] == 'spend') {
            $return = Mage::getModel('simirewardpoints/simiappmapping')->spendPoints();
            $result['simirewardpoint'] = array_merge($result['simirewardpoint'], $return);
        }
        return $result;
    }

}
