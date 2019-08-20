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

class CreditDropdown extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;
    
    public function __construct(
        \Magento\Framework\Locale\FormatInterface $localeFormat
    ) {
        $this->_localeFormat = $localeFormat;
    }
    
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
        if($object->getData('credit_type') !== \Vnecoms\Credit\Model\Source\Type::TYPE_OPTION) return;
        
        if($value && is_array($value)){
            $optionCount = 0;
            foreach($value as $creditValue){
                if(isset($creditValue['delete']) && $creditValue['delete']) continue;
                $optionCount ++;
                if(!isset($creditValue['credit_value']) || 
                    !isset($creditValue['credit_price']) || 
                    !$creditValue['credit_value'] || 
                    !$creditValue['credit_price']
                ) {
                    throw new LocalizedException(__("All store credit value and price must be set '%1'", $attrCode));
                }
            }
            
            if(!$optionCount) throw new LocalizedException(__("Store Credit Value must be set '%1'", $attrCode));
        }elseif($object->getData('credit_type') == \Vnecoms\Credit\Model\Source\Type::TYPE_OPTION){
            throw new LocalizedException(__("Store Credit Value must be set '%1'", $attrCode));
        }
    }
    /**
     * Sort values
     *
     * @param array $data
     * @return array
     */
    protected function _sortValues($data)
    {
        usort($data, [$this, '_sortCreditPrices']);
        return $data;
    }
    
    /**
     * Sort tier price values callback method
     *
     * @param array $a
     * @param array $b
     * @return int
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _sortCreditPrices($a, $b)
    {
        if ($a['credit_value'] != $b['credit_value']) {
            return $a['credit_value'] < $b['credit_value'] ? -1 : 1;
        }
    
        return 0;
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
        if($object->getData('credit_type') == \Vnecoms\Credit\Model\Source\Type::TYPE_OPTION){
            $value = $object->getData($attrCode);
            if($value && is_array($value)){
                foreach($value as $key=>$creditValue){
                    if(isset($creditValue['delete']) && $creditValue['delete']) {
                        unset($value[$key]);
                    }
                    if(isset($value[$key]['delete'])) unset($value[$key]['delete']);
                    
                    $value[$key]['credit_price'] = $this->_localeFormat->getNumber(
                        $creditValue['credit_price']
                    );
                }
                $value = $this->_sortValues($value);
                $value = array_values($value);
                $value = json_encode($value);
                
                $object->setData($attrCode, $value);
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
        if($value){
            if(!is_array($value)) $value = json_decode($value, true);
            if($value === null || !is_array($value)){
                $value = [];
            }
            
            $object->setData($attrCode, $value);
        }
        return $this;
    }
    
}
