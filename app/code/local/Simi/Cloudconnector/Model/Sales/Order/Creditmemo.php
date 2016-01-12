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
class Simi_Cloudconnector_Model_Sales_Order_Creditmemo extends Simi_Cloudconnector_Model_Sales_Order {

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
        $creditmemoId = $data['invoices'];
        $params = array();
        if(isset($data['params']))
            $params = $data['params'];
        if(!$creditmemoId){            
            $offset = $data['offset'];
            $limit = $data['limit'];
            $update = $data['update'];
            $count = $data['count'];
            $information = $this->getListCreditmemo($offset, $limit, $update, $count, $params);
        }else{
            $information = $this->getCreditmemo($creditmemoId);
        }        
        return $information;
    }

    /**
     * get creditmemo collection
     * 
     * @param   boolean  
     * @return   object
     */
    public function getCreditmemoCollection($update){
        $collection = Mage::getModel('sales/order_creditmemo')->getCollection();
        if($update){                        
            $collection->getSelect()->join(array('sync'=>$collection->getTable('cloudconnector/sync')), 
                                               'main_table.entity_id = sync.element_id', array('*'));
            $collection->getSelect()->where('sync.type ='. self::TYPE_CREDITMEMO);
        }
        return $collection;
    }

    /**
     * get creditmemos list
     * 
     * @param   int, int, boolean, boolean, array  
     * @return   json
     */
    public function getListCreditmemo($offset, $limit, $update, $count, $params){        
        $creditmemos = $this->getCreditmemoCollection($update);     
        if($count)
            return $creditmemos->getSize();                    
        if($params)
            foreach ($params as $key => $value) {
            $creditmemos->addFieldToFilter($key, $value);
        }
        if(!$offset)
            $offset = 0;
        if(!$limit)
            $limit = 10;
        $creditmemos->setPageSize($limit);
        $creditmemos->setCurPage($offset/$limit + 1);    
        $creditmemoList = array();        
        foreach ($creditmemos as $creditmemo) {    
            $creditmemoInfo = array();
            $creditmemoInfo = $this->getCreditmemoInfo($creditmemo);       
            $creditmemoList[] = $creditmemoInfo;
            if($update){                
                $this->removeUpdateRecord($creditmemo->getData('id'));
            }
        }
        return $creditmemoList;
    }

    /**
     * get creditmemo information
     *
     * @param   int 
     * @return   json
     */
    public function getCreditmemo($creditmemoId){
        $creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
                    ;           
        $creditmemoInfo = $this->getCreditmemoInfo($creditmemo);    
        return array($creditmemoInfo);
    }

    /**
     * get json information of a creditmemo
     *
     * @param   object 
     * @return   json
     */
    public function getCreditmemoInfo($creditmemo){
        $creditmemoInfo = array();
        $creditmemoInfo['id'] = $creditmemo->getId();        
        $creditmemoInfo['order_currency_code'] = $creditmemo->getData('order_currency_code');
        $creditmemoInfo['base_currency_code'] = $creditmemo->getData('base_currency_code');
        $creditmemoInfo['store_currency_code'] = $creditmemo->getData('store_currency_code');
        $creditmemoInfo['currency_template'] = Mage::helper('core')->currency(1000, true, false);
        $creditmemoInfo['discount_amount'] = $creditmemo->getData('discount_amount');
        $creditmemoInfo['base_discount_amount'] = $creditmemo->getData('base_discount_amount');
        $creditmemoInfo['tax_amount'] = $creditmemo->getData('tax_amount');
        $creditmemoInfo['base_tax_amount'] = $creditmemo->getData('base_tax_amount');
        $creditmemoInfo['tax_percent'] = '';
        $creditmemoInfo['shipping_amount'] = $creditmemo->getData('shipping_amount');
        $creditmemoInfo['base_shipping_amount'] = $creditmemo->getData('base_shipping_amount');
        $creditmemoInfo['coupon'] = $creditmemo->getData('coupon_code') ? array($shipment->getData('coupon_code')) : array();
        $creditmemoInfo['subtotal'] = $creditmemo->getData('subtotal');
        $creditmemoInfo['base_subtotal'] = $creditmemo->getData('base_subtotal');
        $creditmemoInfo['grand_total'] = $creditmemo->getData('grand_total');
        $creditmemoInfo['base_grand_total'] = $creditmemo->getData('base_grand_total');
        $creditmemoInfo['created_at'] = $creditmemo->getData('created_at');
        $creditmemoInfo['updated_at'] = $creditmemo->getData('updated_at');
        $creditmemoInfo['order_id'] = $creditmemo->getData('order_id');
        $creditmemoInfo['adjustment_refund'] = $creditmemo->getData('adjustment_positive');
        $creditmemoInfo['adjustment_fee'] = $creditmemo->getData('adjustment_negative');
        $creditmemoInfo['comments'] = $this->getCreditmemoComments($creditmemo);
        $creditmemoInfo['items'] = $this->getCreditmemoItems($creditmemo);
        if($creditmemo->getData('customer_id')){
            $creditmemoInfo['customer'] = $this->getCustomerOrder($creditmemo->getOrder());
        }
        if($creditmemo->getBillingAddress()){
            $creditmemoInfo['billing_address'] = $this->getCreditmemoAddress($creditmemo->getBillingAddress());
            $creditmemoInfo['bill_name'] = $creditmemo->getBillingAddress()->getData('firstname').' '.$creditmemo->getBillingAddress()->getData('lastname');
        }
        if($creditmemo->getShippingAddress()){
            $creditmemoInfo['shipping_address'] = $this->getCreditmemoAddress($creditmemo->getShippingAddress());                     
            if($creditmemo->getOrder()->getShippingMethod()){
                $creditmemoInfo['shipping'] = $this->getOrderShippingMethod($creditmemo->getOrder());
            }
        }    
        if($creditmemo->getOrder()->getPayment()){
            $creditmemoInfo['payment'] = $this->getPayment($creditmemo->getOrder()->getPayment());
        }    
        return $creditmemoInfo;
    }

    /**
     * get creditmemo address
     *
     * @param   object 
     * @return   json
     */
    public function getCreditmemoAddress($address){
        $addressCreditmemo = array();
        if($address->getData('country_id')){
            $countryModel = Mage::getModel('directory/country')->loadByCode($address->getData('country_id'));
            $countryName = $countryModel->getName();
            $addressCreditmemo['country'] = array(
                                    'code' => $address->getData('country_id'),
                                    'name' => $countryName
                                );
        }
        $addressCreditmemo['id'] = $address->getId();
        $addressCreditmemo['first_name'] = $address->getData('firstname');
        $addressCreditmemo['last_name'] = $address->getData('lastname');
        $addressCreditmemo['phone'] = $address->getData('telephone');        
        $stateCode =  $address->getData('region_id') ? $address->getData('region_id') : '';       
        $stateName =  $address->getData('region') ? $address->getData('region') : '';       
        $addressCreditmemo['state'] = array(
                                    'code' => $stateCode,
                                    'name' => $stateName
                                );$addressCreditmemo['street'] = $address->getData('street');
        $addressCreditmemo['city'] = $address->getData('city');
        $addressCreditmemo['zip'] = $address->getData('postcode');
        return $addressCreditmemo;
    }

    /**
     * get creditmemo items
     *
     * @param   object 
     * @return   json
     */
    public function getCreditmemoItems($creditmemo){
        $items = array();
        foreach ($creditmemo->getAllItems() as $item) {
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
        $itemInfo['refund_qty'] = $item->getData('qty');
        $itemInfo['price'] = $item->getData('price');
        $itemInfo['base_price'] = $item->getData('base_price');
        $itemInfo['tax_percent'] = $item->getData('tax_percent');
        $itemInfo['tax_amount'] = $item->getData('tax_amount');
        $itemInfo['row_total'] = $item->getData('row_total');
        $itemInfo['base_row_total'] = $item->getData('base_row_total');
        $itemInfo['virtual'] = $item->getData('is_virtual');
        // $itemInfo['options'] = $this->getOptionDetail($item->getOptions());
        return $itemInfo;
    }

    /**
     * get creditmemo comments
     *
     * @param   object 
     * @return   json
     */
    public function getCreditmemoComments($creditmemo){
        $comments = array();       
        foreach ($creditmemo->getCommentsCollection() as $comment) {            
            if($comment->getId()){
                $commentInfo = $this->getInfoCommetns($comment);
                $comments[] = $commentInfo;
            }
        }
        return $comments;
    }
  
}