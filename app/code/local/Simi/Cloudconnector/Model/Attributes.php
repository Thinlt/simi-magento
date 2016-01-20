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
class Simi_Cloudconnector_Model_Attributes extends Simi_Cloudconnector_Model_Abstract {

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
        $attributeId = $data['attributes'];
        $params = array();
        if(isset($data['params']))
            $params = $data['params'];
        if(!$attributeId){            
            $offset = $data['offset'];
            $limit = $data['limit'];
            $update = $data['update'];
            $count = $data['count'];
            $information = $this->getListAttributes($offset, $limit, $update, $count, $params);
        }else{
            $information = $this->getAttribute($attributeId);
        }        
        return $information;
    }

    /**
     * get attribute collection
     * 
     * @param   boolean  
     * @return   object
     */
    public function getAttributeCollection($update){
        $mainAttributes = array('sku', 'name', 'type_id', 'entity_type_id','entity_id', 'price', 'final_price','attribute_set_id', 
                                'description', 'short_description', 'status', 'created_at', 
                                 'updated_at', 'tax_class_id', 'attribute_set_id', 
                                 'minimal_price', 'updated_at', 'thumbnail', 'small_image', 'visibility',
                                 'media_gallery', 'media', 'weight','length','width','height', 'image',
                                 'options_container','page_layout','custom_design'
                                 );
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                    ->addFieldToFilter('attribute_code', array('nin' => $mainAttributes))
                    ->addFieldToFilter('frontend_label', array('neq' => ''))
                    ->addFieldToFilter('frontend_input', array('neq' => ''))
                    ;
        if($update){                        
            $attributes->getSelect()->join(array('sync'=>$attributes->getTable('cloudconnector/sync')), 
                                               'main_table.attribute_id = sync.element_id', array('*'));
            $attributes->getSelect()->where('sync.type ='. self::TYPE_ATTRIBUTE);
        }
        return $attributes;
    }

    /**
     * get attributes list
     * 
     * @param   int, int, boolean, boolean, array  
     * @return   json
     */
    public function getListAttributes($offset, $limit, $update, $count, $params){
        $attributes = $this->getAttributeCollection($update);  
        if($count)
            return $attributes->getSize(); 
        if(!$offset)
            $offset = 0;
        if(!$limit)
            $limit = 10;
        $attributes->setPageSize($limit);
        $attributes->setCurPage($offset/$limit + 1);        
        if($params)
            foreach ($params as $key => $value) {
            $attributes->addFieldToFilter($key, $value);
        }
        $customerList = array();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront()) {  // only return attribute visible in font-end [kenshin]
                $attributeInfo = array();
                $attributeInfo['id'] = $attribute->getData('attribute_id');
                $attributeInfo['name'] = $attribute->getData('frontend_label');
                $attributeInfo['code'] = $attribute->getData('attribute_code');
                $attributeInfo['type'] = $attribute->getData('frontend_input');
                $attributeInfo['is_visible_on_front'] = $attribute->getData('is_visible_on_front');
                $attributeInfo['updated_at'] = now();
                $attributeInfo['created_at'] = now();
                if ($attributeInfo['type'] == 'multiselect' || $attributeInfo['type'] == 'select')
                    $attributeInfo['values'] = $this->getAttributeLabels($attribute);
                $attributeList[] = $attributeInfo;
                if ($update) {
                    $this->removeUpdateRecord($attribute->getData('id'));
                }
            }
        }
        return $attributeList;
    }

    /**
     * get attribute information
     *
     * @param   int 
     * @return   json
     */
    public function getAttribute($attributeId){
        $storeId = 0;
        $attribute = Mage::getModel('catalog/product')
                ->setStoreId($storeId)
                ->getResource()
                ->getAttribute($attributeId);
        $attributeInfo = array();
        $attributeInfo['id'] = $attribute->getData('attribute_id');
        $attributeInfo['name'] = $attribute->getData('frontend_label');
        $attributeInfo['code'] = $attribute->getData('attribute_code');
        $attributeInfo['type'] = $attribute->getData('frontend_input');            
        $attributeInfo['updated_at'] = now();
        $attributeInfo['created_at'] = now();
        if($attributeInfo['type'] == 'multiselect' || $attributeInfo['type'] == 'select')
            $attributeInfo['values'] = $this->getAttributeLabels($attribute);

        return array($attributeInfo);
    }

    /**
     * get attribute labels
     *
     * @param   object 
     * @return   json
     */
    public function getAttributeLabels($attribute){
        if ($attribute) {
            $options = array();
            if ($attribute->usesSource()) {
                foreach ($attribute->getSource()->getAllOptions() as $optionId => $optionValue) {
                    if (is_array($optionValue)) {
                        if($optionValue['label'])
                            $options[] = $optionValue['label'];
                    } else {
                         if($optionValue)
                            $options[] = $optionValue;
                    }    
                }
            } 
        }
        return $options;
    }

    /**
     * pull data from cloud
     * 
     * @param   array  
     * @return   
     */
    public function pull($data){        
        $this->createAttribute($data);
    }

    /**
     * create customer group
     *
     * @param   json 
     * @return   json
     */
    public function createAttribute($data){
        // Comming soon....        
    }
    
}