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
class Simi_Cloudconnector_Model_Sales_Order_Invoice extends Simi_Cloudconnector_Model_Sales_Order {

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
        $invoiceId = $data['invoices'];
        $params = array();
        if(isset($data['params']))
            $params = $data['params'];
        if(!$invoiceId){            
            $offset = $data['offset'];
            $limit = $data['limit'];
            $update = $data['update'];
            $count = $data['count'];
            $information = $this->getListInvoice($offset, $limit, $update, $count, $params);
        }else{
            $information = $this->getInvoice($invoiceId);
        }        
        return $information;
    }

    /**
     * get invoice collection
     * 
     * @param   boolean  
     * @return   object
     */
    public function getInvoiceCollection($update){
        $collection = Mage::getModel('sales/order_invoice')->getCollection();
        if($update){                        
            $collection->getSelect()->join(array('sync'=>$collection->getTable('cloudconnector/sync')), 
                                               'main_table.entity_id = sync.element_id', array('*'));
            $collection->getSelect()->where('sync.type ='. self::TYPE_INVOICE);
        }
        return $collection;
    }

    /**
     * get invoices list
     * 
     * @param   int, int, boolean, boolean, array  
     * @return   json
     */
    public function getListInvoice($offset, $limit, $update, $count, $params){        
        $invoices = $this->getInvoiceCollection($update); 
        if($count)
            return $invoices->getSize(); 
        if(!$offset)
            $offset = 0;
        if(!$limit)
            $limit = 10;
        $invoices->setPageSize($limit);
        $invoices->setCurPage($offset/$limit + 1);                          
        if($params)
            foreach ($params as $key => $value) {
            $invoices->addFieldToFilter($key, $value);
        }
        $invoiceList = array();
        foreach ($invoices as $invoice) {            
            $invoiceInfo = array();
            $invoiceInfo = $this->getInvoiceInfo($invoice);       
            $invoiceList[] = $invoiceInfo;
            if($update){                
                $this->removeUpdateRecord($invoice->getData('id'));
            }
        }
        return $invoiceList;
    }

    /**
     * get invoice information
     *
     * @param   int 
     * @return   json
     */
    public function getInvoice($invoiceId){
        $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
                    ;           
        $invoiceInfo = $this->getInvoiceInfo($invoice);    
        return array($invoiceInfo);
    }

    /**
     * get json information of an invoice
     *
     * @param   object 
     * @return   json
     */
    public function getInvoiceInfo($invoice){
        $invoiceInfo = array();
        $invoiceInfo['id'] = $invoice->getId();        
        $invoiceInfo['order_currency_code'] = $invoice->getData('order_currency_code');
        $invoiceInfo['base_currency_code'] = $invoice->getData('base_currency_code');
        $invoiceInfo['store_currency_code'] = $invoice->getData('store_currency_code');
        $invoiceInfo['currency_template'] = Mage::helper('core')->currency(1000, true, false);
        $invoiceInfo['discount_amount'] = $invoice->getData('discount_amount');
        $invoiceInfo['base_discount_amount'] = $invoice->getData('base_discount_amount');
        $invoiceInfo['tax_amount'] = $invoice->getData('tax_amount');
        $invoiceInfo['base_tax_amount'] = $invoice->getData('base_tax_amount');
        $invoiceInfo['tax_percent'] = '';
        $invoiceInfo['shipping_amount'] = $invoice->getData('shipping_amount');
        $invoiceInfo['base_shipping_amount'] = $invoice->getData('base_shipping_amount');
        $invoiceInfo['coupon'] = $invoice->getData('coupon_code') ? array($invoice->getData('coupon_code')) : array();
        $invoiceInfo['subtotal'] = $invoice->getData('subtotal');
        $invoiceInfo['base_subtotal'] = $invoice->getData('base_subtotal');
        $invoiceInfo['grand_total'] = $invoice->getData('grand_total');
        $invoiceInfo['base_grand_total'] = $invoice->getData('base_grand_total');
        $invoiceInfo['created_at'] = $invoice->getData('created_at');
        $invoiceInfo['updated_at'] = $invoice->getData('updated_at');
        $invoiceInfo['order_id'] = $invoice->getData('order_id');
        $invoiceInfo['comments'] = $this->getInvoiceComments($invoice);
        $invoiceInfo['items'] = $this->getInvoiceItems($invoice);
        if($invoice->getData('customer_id')){
            $invoiceInfo['customer'] = $this->getCustomerOrder($invoice->getOrder());
        }
        if($invoice->getBillingAddress()){
            $invoiceInfo['billing_address'] = $this->getInvoiceAddress($invoice->getBillingAddress());
            $invoiceInfo['bill_name'] = $invoice->getBillingAddress()->getData('firstname').' '.$invoice->getBillingAddress()->getData('lastname');
        }
        if($invoice->getShippingAddress()){
            $invoiceInfo['shipping_address'] = $this->getInvoiceAddress($invoice->getShippingAddress());                
            if($invoice->getOrder()->getShippingMethod()){
                $invoiceInfo['shipping'] = $this->getOrderShippingMethod($invoice->getOrder());
            }
        }    
        if($invoice->getOrder()->getPayment()){
            $invoiceInfo['payment'] = $this->getPayment($invoice->getOrder()->getPayment());
        }    
        return $invoiceInfo;
    }

    /**
     * get invoice address
     *
     * @param   object 
     * @return   json
     */
    public function getInvoiceAddress($address){
        $addressInvoice = array();
        if($address->getData('country_id')){
            $countryModel = Mage::getModel('directory/country')->loadByCode($address->getData('country_id'));
            $countryName = $countryModel->getName();
            $addressInvoice['country'] = array(
                                    'code' => $address->getData('country_id'),
                                    'name' => $countryName
                                );
        }
        $addressInvoice['id'] = $address->getId();
        $addressInvoice['first_name'] = $address->getData('firstname');
        $addressInvoice['last_name'] = $address->getData('lastname');
        $addressInvoice['phone'] = $address->getData('telephone');        
        $stateCode =  $address->getData('region_id') ? $address->getData('region_id') : '';       
        $stateName =  $address->getData('region') ? $address->getData('region') : '';       
        $addressInvoice['state'] = array(
                                    'code' => $stateCode,
                                    'name' => $stateName
                                );
        $addressInvoice['street'] = $address->getData('street');
        $addressInvoice['city'] = $address->getData('city');
        $addressInvoice['zip'] = $address->getData('postcode');
        return $addressInvoice;
    }

    /**
     * get invoice items
     *
     * @param   object 
     * @return   json
     */
    public function getInvoiceItems($invoice){
        $items = array();
        foreach ($invoice->getAllItems() as $item) {
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
        $itemInfo['qty'] = $item->getData('qty');
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
     * get invoice comments
     *
     * @param   object 
     * @return   json
     */
    public function getInvoiceComments($invoice){
        $comments = array();       
        foreach ($invoice->getCommentsCollection() as $comment) {            
            if($comment->getId()){
                $commentInfo = $this->getInfoCommetns($comment);
                $comments[] = $commentInfo;
            }
        }
        return $comments;
    }

}