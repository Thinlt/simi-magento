<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Connector
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Connector Model
 * 
 * @category 	Simi
 * @package 	Simi_Cloudconnector
 * @author  	Simi Developer
 */
class Simi_Cloudconnector_Model_Abstract extends Mage_Core_Model_Abstract {

    const TYPE_CUSTOMER_GROUP   = 1;
    const TYPE_CUSTOMER = 2;
    const TYPE_CATALOG_CATEGORY = 3;
    const TYPE_ATTRIBUTE    = 4;
    const TYPE_ATTRIBUTESET = 5;
    const TYPE_PRODUCT  = 6;
    const TYPE_QUOTE    = 7;
    const TYPE_ORDER    = 8;
    const TYPE_INVOICE  = 9;
    const TYPE_SHIPMENT = 10;
    const TYPE_CREDITMEMO   = 11;

    /** get helper
    *
    * @param 
    * @return Simi_Cloudconnector_Helper_Data
    **/
    public function _helper() {
        return Mage::helper('cloudconnector');
    }

    /** get controller name
    *
    * @param 
    * @return string
    **/
    public function getControllerName() {
        $request = Mage::app()->getFrontController()->getRequest();
        $name = $request->getRequestedRouteName() . '_' .
                $request->getRequestedControllerName() . '_' .
                $request->getRequestedActionName();
        return $name;
    }

    /** dispacth event
    *
    * @param string, array
    * @return 
    **/
    public function eventChangeData($name_event, $value) {
        Mage::dispatchEvent($name_event, $value);
    }

    /** dispacth event
    *
    * @param string, array
    * @return 
    **/
    public function removeUpdateRecord($recordId) {
        $this->_helper()->removeUpdateRecord($recordId);
    }   
        
}