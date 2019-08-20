<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\Vendors\Model\ResourceModel\Vendor;

/**
 * Cms page mysql resource
 */
class Fieldset extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_vendor_fieldset', 'fieldset_id');
    }
    
    /**
     * Get the list of attributes
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return array:
     */
    public function getAttributes(\Magento\Framework\Model\AbstractModel $object){
        $table = $this->getTable('ves_vendor_fieldset_attr');
        $attrTbl = $this->getTable('eav_attribute');
        $readCollection = $this->getConnection();
        $sql = "SELECT e.sort_order, e1.attribute_code, e1.attribute_id FROM $table as e"
            ." INNER JOIN $attrTbl as e1 ON e.attribute_id = e1.attribute_id WHERE e.fieldset_id=".$object->getId()
            ." ORDER BY e.sort_order ASC";
        $results = $readCollection->fetchAll($sql);
        
        $attributes = [];
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        foreach($results as $attributeData){
            $attr = $om->create('Vnecoms\Vendors\Model\Attribute');
            $attr->load($attributeData['attribute_id']);
            $attr->setSortOrder($attributeData['sort_order']);
            $attributes[] = $attr;
        }
        return $attributes;
    }
    
    
    public function saveAttributes(\Magento\Framework\Model\AbstractModel $object, $attributes = array()){
        $table = $this->getTable('ves_vendor_fieldset_attr');
        $deleteSql = "DELETE FROM {$table} WHERE fieldset_id = {$object->getId()}";
        $this->getConnection()->query($deleteSql);
        if(sizeof($attributes)){
            $sql = "INSERT INTO {$table}(fieldset_id,attribute_id,sort_order) VALUES ";
            foreach($attributes as $attributeId=>$order){
                $sql.= "({$object->getId()},{$attributeId},{$order}),";
            }
            $sql = trim($sql,",").";";
            $this->getConnection()->query($sql);
        }
    }
}
