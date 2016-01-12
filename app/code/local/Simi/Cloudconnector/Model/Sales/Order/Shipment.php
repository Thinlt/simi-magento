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
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @copyright   Copyright (c) 2015 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Connector Model Customer
 * 
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @author      Simi Developer
 */
class Simi_Cloudconnector_Model_Sales_Order_Shipment extends Simi_Cloudconnector_Model_Sales_Order {

    /**
     * Internal constructor
     */
    public function _construct() {
        parent::_construct();
    }

    /**
     * get api result
     * 
     * @param   array  
     * @return   json
     */
    public function run($data){      
        $shipmentId = $data['shipments'];
        $params = array();
        if(isset($data['params']))
            $params = $data['params'];
        if(!$shipmentId){            
            $offset = $data['offset'];
            $limit = $data['limit'];
            $update = $data['update'];
            $count = $data['count'];
            $information = $this->getListShipment($offset, $limit, $update, $count, $params);
        }else{
            $information = $this->getShipment($shipmentId);
        }        
        return $information;
    }

    /**
     * get shipment collection
     * 
     * @param   boolean  
     * @return   object
     */
    public function getShipmentCollection($update){
        $collection = Mage::getModel('sales/order_shipment')->getCollection();
        if($update){                        
            $collection->getSelect()->join(array('sync'=>$collection->getTable('cloudconnector/sync')), 
                                               'main_table.entity_id = sync.element_id', array('*'));
            $collection->getSelect()->where('sync.type ='. self::TYPE_SHIPMENT);
        }
        return $collection;
    }

    /**
     * get shipments list
     * 
     * @param   int, int, boolean, boolean, array  
     * @return   json
     */
    public function getListShipment($offset, $limit, $update, $count, $params){        
        $shipments = $this->getShipmentCollection($update); 
        if($count)
            return $shipments->getSize();     
        if(!$offset)
            $offset = 0;
        if(!$limit)
            $limit = 10;
        $shipments->setPageSize($limit);
        $shipments->setCurPage($offset/$limit + 1);                      
        if($params)
            foreach ($params as $key => $value) {
            $shipments->addFieldToFilter($key, $value);
        }
        $shipmentList = array();
        foreach ($shipments as $shipment) {            
            $shipmentInfo = array();
            $shipmentInfo = $this->getShipmentInfo($shipment);       
            $shipmentList[] = $shipmentInfo;
            if($update){                
                $this->removeUpdateRecord($shipment->getData('id'));
            }
        }
        return $shipmentList;
    }

    /**
     * get shipment information
     *
     * @param   int 
     * @return   json
     */
    public function getShipment($shipmentId){
        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
                    ;           
        $shipmentInfo = $this->getShipmentInfo($shipment);    
        return array($shipmentInfo);
    }

    /**
     * get json information of a shipment
     *
     * @param   object 
     * @return   json
     */
    public function getShipmentInfo($shipment){
        $shipmentInfo = array();
        $shipmentInfo['id'] = $shipment->getId();                
        $shipmentInfo['created_at'] = $shipment->getData('created_at');
        $shipmentInfo['updated_at'] = $shipment->getData('updated_at');
        $shipmentInfo['order_id'] = $shipment->getData('order_id');
        $shipmentInfo['comments'] = $this->getShipmentComments($shipment);
        $shipmentInfo['items'] = $this->getShipmentItems($shipment);
        if($shipment->getData('customer_id')){
            $shipmentInfo['customer'] = $this->getCustomerOrder($shipment->getOrder());
        }
        if($shipment->getBillingAddress()){
            $shipmentInfo['billing_address'] = $this->getShipmentAddress($shipment->getBillingAddress());
            $shipmentInfo['bill_name'] = $shipment->getBillingAddress()->getData('firstname').' '.$shipment->getBillingAddress()->getData('lastname');
        }
        if($shipment->getShippingAddress()){
            $shipmentInfo['shipping_address'] = $this->getShipmentAddress($shipment->getShippingAddress());                
            if($shipment->getOrder()->getShippingMethod()){
                $shipmentInfo['shipping'] = $this->getOrderShippingMethod($shipment->getOrder());
            }
        }    
         //check - payment is acitve by Max
        $payment_methods = $this->getActivPaymentMethods(); 
        if($shipment->getOrder()->getPayment() && $payment_methods[$shipment->getOrder()->getPayment()->getMethod()]){
            $shipmentInfo['payment'] = $this->getPayment($shipment->getOrder()->getPayment());
        }    
        return $shipmentInfo;
    }

    /**
     * get shipment address
     *
     * @param   object 
     * @return   json
     */
    public function getShipmentAddress($address){
        $addressShipment = array();
        if($address->getData('country_id')){
            $countryModel = Mage::getModel('directory/country')->loadByCode($address->getData('country_id'));
            $countryName = $countryModel->getName();
            $addressShipment['country'] = array(
                                    'code' => $address->getData('country_id'),
                                    'name' => $countryName
                                );
        }
        $addressShipment['id'] = $address->getId();
        $addressShipment['first_name'] = $address->getData('firstname');
        $addressShipment['last_name'] = $address->getData('lastname');
        $addressShipment['phone'] = $address->getData('telephone');        
        $addressShipment['state'] = array(
                                    'code' => $address->getData('region_id'),
                                    'name' => $address->getData('region')
                                );
        $addressShipment['street'] = $address->getData('street');
        $addressShipment['city'] = $address->getData('city');
        $addressShipment['zip'] = $address->getData('postcode');
        return $addressShipment;
    }

    /**
     * get shipment items
     *
     * @param   object 
     * @return   json
     */
    public function getShipmentItems($shipment){
        $items = array();
        foreach ($shipment->getAllItems() as $item) {
            $itemInfo = $this->getInfoItems($item);
            $items[] = $itemInfo;
        }
        return $items;
    }

    /**
     * get item information
     *
     * @param   object 
     * @return   json
     */
    public function getInfoItems($item){
        $itemInfo = array();
        $itemInfo['id'] = $item->getId();
        $itemInfo['product_id'] = $item->getData('product_id');
        $itemInfo['order_item_id'] = $item->getData('order_item_id');
        $itemInfo['name'] = $item->getData('name');
        $itemInfo['sku'] = $item->getData('sku');
        $itemInfo['ship_qty'] = $item->getData('qty');
        $itemInfo['price'] = $item->getData('price');
        $itemInfo['row_total'] = $item->getData('row_total') ? $item->getData('row_total') : '';
        // $itemInfo['options'] = $this->getOptionDetail($item->getOptions());
        return $itemInfo;
    }

    /**
     * get shipment comments
     *
     * @param   object 
     * @return   json
     */
    public function getShipmentComments($shipment){
        $comments = array();       
        foreach ($shipment->getCommentsCollection() as $comment) {            
            if($comment->getId()){
                $commentInfo = $this->getInfoCommetns($comment);
                $comments[] = $commentInfo;
            }
        }
        return $comments;
    }
  
}