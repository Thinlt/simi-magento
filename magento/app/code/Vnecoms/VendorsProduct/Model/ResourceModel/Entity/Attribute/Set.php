<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute;

/**
 * Cms page mysql resource
 */
class Set extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_vendor_product_attribute_set', 'attribute_set_id');
    }
    
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object){
        if ($object->getGroups()) {
            /* @var $group VES_VendorsProduct_Model_Entity_Attribute_Group */
            foreach ($object->getGroups() as $group) {

                $group->setAttributeSetId($object->getId());
                if ($group->itemExists() && !$group->getId()) {
                    continue;
                }
                
                $group->save();
            }
        }
        if ($object->getRemoveGroups()) {
            foreach ($object->getRemoveGroups() as $group) {
                /* @var $group VES_VendorsProduct_Model_Entity_Attribute_Group */
                $group->delete();
            }
            //Mage::getResourceModel('eav/entity_attribute_group')->updateDefaultGroup($object->getId());
        }
        if ($object->getRemoveAttributes()) {
            foreach ($object->getRemoveAttributes() as $attribute) {
                /* @var $attribute Mage_Eav_Model_Entity_Attribute */
                $attribute->deleteEntity();
            }
        }
        
        return parent::_afterSave($object);
    }

}
