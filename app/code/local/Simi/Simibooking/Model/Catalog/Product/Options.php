<?php

/**
 * Created by PhpStorm.
 * User: Scott
 * Date: 1/22/2016
 * Time: 11:23 AM
 */
class Simi_Simibooking_Model_Catalog_Product_Options
{
    public function getOptions($product) {
        $type = $product->getTypeId();
        switch ($type) {
            case Simi_Simibooking_Helper_Data::BOOKABLE_TYPE_CODE:
                return Mage::getModel('simibooking/catalog_product_options_bookable')->getOptions($product);
                break;
            case Simi_Simibooking_Helper_Data::RESERVATION_TYPE_CODE :
                return Mage::getModel('simibooking/catalog_product_options_reservation')->getOptions($product);
                break;
        }
    }
	
	 public function getPriceModel($product) {
        $type = $product->getTypeId();
        switch ($type) {          
            case Mage_Catalog_Model_Product_Type::TYPE_BUNDLE :
                return Mage::getSingleton('connector/catalog_product_options_bundle')->getPrice($product);
                break;            
            case Mage_Catalog_Model_Product_Type::TYPE_GROUPED :
                return Mage::getSingleton('connector/catalog_product_options_grouped')->getPrice($product);
                break;            
        }
	 }
}