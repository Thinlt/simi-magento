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
 * Cloudconnector Model Sales Quote
 * 
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @author      Simi Developer
 */
class Simi_Cloudconnector_Model_Sales_Quote extends Simi_Cloudconnector_Model_Abstract {

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
        $quoteId = $data['quotes'];
        $params = array();
        if(isset($data['params']))
            $params = $data['params'];
        if(!$quoteId){            
            $offset = $data['offset'];
            $limit = $data['limit'];
            $update = $data['update'];
            $count = $data['count'];
            $information = $this->getListQuote($offset, $limit, $update, $count, $params);
        }else{
            $information = $this->getQuote($quoteId);
        }        
        return $information;
    }

    /**
     * get quote collection
     * 
     * @param   boolean  
     * @return   object
     */
    public function getQuoteCollection($update){
        $collection = Mage::getModel('sales/quote')->getCollection();
        if($update){                        
            $collection->getSelect()->join(array('sync'=>$collection->getTable('cloudconnector/sync')), 
                                               'main_table.entity_id = sync.element_id', array('*'));
            $collection->getSelect()->where('sync.type ='. self::TYPE_QUOTE);
        }

        return $collection;
    }

    /**
     * get quotes list
     * 
     * @param   int, int, boolean, boolean, array  
     * @return   json
     */
    public function getListQuote($offset, $limit, $update, $count, $params){        
        $quotes = $this->getQuoteCollection($update);  
        if($count)
            return $this->getQuoteCollection()->getSize();
        if(!$offset)
            $offset = 0;
        if(!$limit)
            $limit = 10;
        $quotes->setPageSize($limit);
        $quotes->setCurPage($offset/$limit + 1);               
        if($params)
            foreach ($params as $key => $value) {
                $quotes->addFieldToFilter($key, $value);
            }  
        $quoteList = array();
        foreach ($quotes as $quote) {            
            $quoteInfo = array();
            $quoteInfo = $this->getQuoteInfo($quote);       
            $quoteList[] = $quoteInfo;
            if($update){                
                $this->removeUpdateRecord($quote->getData('id'));
            }
        }
        return $quoteList;
    }

    /**
     * get quote information
     *
     * @param   int 
     * @return   json
     */
    public function getQuote($quoteId){
        $quote = Mage::getModel('sales/quote')->load($quoteId);
                    ;           
        $quoteInfo = $this->getQuoteInfo($quote);    
        return array($quoteInfo);
    }

    /**
     * get json information of a quote
     *
     * @param   object 
     * @return   json
     */
    public function getQuoteInfo($quote){        
        $quoteInfo = array();
        $quoteInfo['id'] = $quote->getId();
        $quoteInfo['quote_currency_code'] = $quote->getData('quote_currency_code');
        $quoteInfo['base_currency_code'] = $quote->getData('base_currency_code');
        $quoteInfo['store_currency_code'] = $quote->getData('store_currency_code');
        $quoteInfo['currency_template'] = Mage::helper('core')->currency(1000, true, false);
        $quoteInfo['coupon'] = $quote->getData('coupon_code') ? array($quote->getData('coupon_code')) : array();
        $quoteInfo['subtotal'] = $quote->getData('subtotal');
        $quoteInfo['base_subtotal'] = $quote->getData('base_subtotal');
        $quoteInfo['grand_total'] = $quote->getData('grand_total');
        $quoteInfo['base_grand_total'] = $quote->getData('base_grand_total');
        $quoteInfo['created_at'] = $quote->getData('created_at');
        $quoteInfo['updated_at'] = $quote->getData('updated_at');
        $quoteInfo['items'] = $this->getQuoteItems($quote);
        if($quote->getData('customer_id')){
            $quoteInfo['customer'] = $this->getCustomerQuote($quote);
        }
        if($quote->getBillingAddress()){
            $quoteInfo['billing_address'] = $this->getQuoteAddress($quote->getBillingAddress());
            $quoteInfo['bill_name'] = $quote->getBillingAddress()->getData('firstname').' '.$quote->getBillingAddress()->getData('lastname');
        }
        if($quote->getShippingAddress()){
            $quoteInfo['shipping_address'] = $this->getQuoteAddress($quote->getShippingAddress());                
            if($quote->getShippingAddress()->getShippingMethod()){
                $quoteInfo['shipping'] = $this->getQuoteShippingMethod($quote->getShippingAddress()->getShippingMethod());
            }
        }
        // if($quote->getPayment()){
        //     $quoteInfo['payment'] = $this->getQuotePayment($quote->getPayment());
        // }

        return $quoteInfo;
    }

    /**
     * get quote customer
     *
     * @param   object 
     * @return   json
     */
    public function getCustomerQuote($quote){
        $customerQuote = array();
        $customerQuote['customer_id'] = $quote->getData('customer_id');
        $customerQuote['customer_email'] = $quote->getData('customer_email');
        $customerQuote['customer_group_id'] = $quote->getData('customer_group_id');
        $customerQuote['customer_first_name'] = $quote->getData('customer_firstname');
        $customerQuote['customer_last_name'] = $quote->getData('customer_lastname');
        $customerQuote['customer_name'] = $quote->getData('customer_firstname').' '.$quote->getData('customer_lastname');
        return $customerQuote;
    }

    /**
     * get quote address
     *
     * @param   object 
     * @return   json
     */
    public function getQuoteAddress($address){
        $addressQuote = array();
        if($address->getData('country_id')){
            $countryModel = Mage::getModel('directory/country')->loadByCode($address->getData('country_id'));
            $countryName = $countryModel->getName();
            $addressQuote['country'] = array(
                                    'code' => $address->getData('country_id'),
                                    'name' => $countryName
                                );
        }
        $addressQuote['id'] = $address->getId();
        $addressQuote['first_name'] = $address->getData('firstname') ? $address->getData('firstname') : '';
        $addressQuote['last_name'] = $address->getData('lastname') ? $address->getData('lastname') : '';
        $addressQuote['phone'] = $address->getData('telephone') ? $address->getData('telephone') : '';        
        $stateCode =  $address->getData('region_id') ? $address->getData('region_id') : '';       
        $stateName =  $address->getData('region') ? $address->getData('region') : '';       
        $addressQuote['state'] = array(
                                    'code' => $stateCode,
                                    'name' => $stateName
                                );
        $addressQuote['street'] = $address->getData('street') ? $address->getData('street') : '';
        $addressQuote['city'] = $address->getData('city') ? $address->getData('city') : '';
        $addressQuote['zip'] = $address->getData('postcode') ? $address->getData('postcode') : '';
        return $addressQuote;
    }

    /**
     * get quote payment
     *
     * @param   object 
     * @return   json
     */
    public function getQuotePayment($payment){
        $code = $payment->getMethodInstance()->getCode();
        $title = $payment->getMethodInstance()->getTitle();
        return array(
                'title' => $title,
                'method_code' => $code
                );
    }

    /**
     * get quote shipping method
     *
     * @param   object 
     * @return   json
     */
    public function getQuoteShippingMethod($method){
        $code = $method;
        return array(
                'cost' => 2,
                'method_code' => $code
                );
    }

    /**
     * get quote items
     *
     * @param   object 
     * @return   json
     */
    public function getQuoteItems($quote){
        $items = array();
        foreach ($quote->getAllItems() as $item) {
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

    public function getOptionDetail($options){
        $optionDetail = array();
        foreach ($options as $option) {            
            Zend_debug::dump($option->getData());die();
        }
        // $optionDetail[''] = $option->getData('');
    }
}