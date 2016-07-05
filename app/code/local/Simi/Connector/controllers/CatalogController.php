<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Connector
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Catalog Controller
 * 
 * @category    
 * @package     Connector
 * @author      Developer
 */
class Simi_Connector_CatalogController extends Simi_Connector_Controller_Action {
    public function preDispatch()
    {
        parent::preDispatch();
        $data = $this->getData();

        if(isset($data->category_id) && $data->category_id){
            $category = Mage::getModel('catalog/category')->load($data->category_id);
            if(isset($data->auction) && $data->auction){
                $category->setIsAnchor(1)
                    ->setName(Mage::helper('core')->__('Auctions'))
                    ->setDisplayMode('PRODUCTS');
            }

            Mage::register('current_category', $category);
        }else{
            $category = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId());
            if(isset($data->auction) && $data->auction){
                $category->setIsAnchor(1)
                    ->setName(Mage::helper('core')->__('Auctions'))
                    ->setDisplayMode('PRODUCTS');
            }

            Mage::register('current_category', $category);
        }
    }

    /**
     * index action
     */
    protected function _helper() {
        return Mage::helper('connector');
    }

    public function indexAction() {
        echo "Not Thing!";
        $this->_helper()->convertToPlist();
        die();
    }

    public function get_spot_productsAction() {
        $data = $this->getData();
        $information = Mage::getModel('connector/catalog_product')->getSpotProduct($data);
        $this->_printDataJson($information);
    }

    public function get_all_productsAction() {
        $data = $this->getData();
        $information = Mage::getModel('connector/catalog_product')->getAllProducts($data);
        $this->_printDataJson($information);
    }

    public function get_product_detailAction() {
        $data = $this->getData();
        $information = Mage::getModel('connector/catalog_product')->getDetail($data);
        $this->_printDataJson($information);
    }

    public function get_related_productsAction() {
        $data = $this->getData();
        $information = Mage::getModel('connector/catalog_product')->getRelatedProducts($data);
        $this->_printDataJson($information);
    }

    public function search_productsAction() {
        $data = $this->getData();        
        $information = Mage::getModel('connector/catalog_product')->getSearchProducts($data);
        $this->_printDataJson($information);
    }

    public function get_categoriesAction() {
        $data = $this->getData();        
        $device_id = $this->getDeviceId();      
        $information = Mage::getModel('connector/catalog_category')->getCategories($data, $device_id);
        $this->_printDataJson($information);
    }

    public function get_category_productsAction() {
        $data = $this->getData();               
        $information = Mage::getModel('connector/catalog_product')->getCategoryProduct($data);
        $this->_printDataJson($information);
    }
    
    public function get_product_reviewAction(){
        $data = $this->getData();
        $information = Mage::getModel('connector/catalog_product')->getProductReview($data);
        $this->_printDataJson($information);
    }

    public function deep_linkAction(){
        $data = $this->getData();
        $information = Mage::getModel('connector/catalog_product')->getDeepLink($data);
        $this->_printDataJson($information);
    }
}
