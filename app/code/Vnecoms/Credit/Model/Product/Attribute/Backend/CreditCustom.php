<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog product tier price backend attribute model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Vnecoms\Credit\Model\Product\Attribute\Backend;

use Magento\Framework\Exception\LocalizedException;

class CreditCustom extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Validate object
     *
     * @param \Magento\Framework\DataObject $object
     * @return bool
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validate($object){
        parent::validate($object);
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        
        if($object->getData('credit_type') !== \Vnecoms\Credit\Model\Source\Type::TYPE_RANGE) return;
        
        if($value && is_array($value)){
            if(!isset($value['from'])) throw new LocalizedException(__("'From' value of Store Credit must be set '%1'", $attrCode));
            if(!isset($value['to'])) throw new LocalizedException(__("'To' value of Store Credit must be set '%1'", $attrCode));
            return true;
        }elseif($object->getData('credit_type') == \Vnecoms\Credit\Model\Source\Type::TYPE_RANGE){
            throw new LocalizedException(__("Store Credit Value must be set '%1'", $attrCode));
        }
        return true;
    }

    /**
     * Before save method
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $this->getDefaultValue();
        if($object->getData('credit_type') == \Vnecoms\Credit\Model\Source\Type::TYPE_RANGE){
            $value = $object->getData($attrCode);
            if($value && is_array($value)){
                $value = json_encode($value);                
            }
        }
        $object->setData($attrCode, $value);
        return parent::beforeSave($object);
    }
    
    /**
     * After load method
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @codeCoverageIgnore
     */
    public function afterLoad($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if($value && !is_array($value)){
            $value = json_decode($value,true);
        }
        $value = is_array($value)?$value:[];
        
        $object->setData($attrCode, $value);
        return $this;
    }
    
}
