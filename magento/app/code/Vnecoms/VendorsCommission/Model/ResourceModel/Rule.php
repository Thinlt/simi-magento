<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsCommission\Model\ResourceModel;

/**
 * Cms page mysql resource
 */
class Rule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_vendor_commission_rule', 'rule_id');
    }

    /**
     * Before save object.
     * 
     * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::_beforeSave()
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object){
        parent::_beforeSave($object);
        
        if($object->getData('commission_by') == \Vnecoms\VendorsCommission\Model\Rule::COMMISSION_BY_FIXED_AMOUNT){
            $object->setData('commission_action','');
        }
        if(is_array($object->getWebsiteIds())){
            $object->setWebsiteIds(implode(",", $object->getWebsiteIds()));
        }
        if(is_array($object->getVendorGroupIds())){
            $object->setVendorGroupIds(implode(",", $object->getVendorGroupIds()));
        }
        return $this;
    }
    
    /**
     * After load object
     * 
     * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::_afterLoad()
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object){
        parent::_afterLoad($object);
        
        $object->setData('vendor_group_ids',explode(",", $object->getData('vendor_group_ids')));
        $object->setData('website_ids',explode(",", $object->getData('website_ids')));
    }
}
