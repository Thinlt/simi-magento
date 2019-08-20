<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Account\Create;

class Fieldset extends \Magento\Framework\View\Element\Template
{
    /**
     * Fields list
     * @var array
     */
    protected $_fields;
    
    /**
     * Get fieldset object
     * @return \Vnecoms\Vendors\Model\Vendor\Fieldset
     */
    public function getFieldset()
    {
        return $this->getData('fieldset');
    }
    
    /**
     * Get title
     * @return string
     */
    public function getTitle()
    {
        return $this->getFieldset()->getTitle();
    }
    
    /**
     * Get fields list
     * @return multitype:
     */
    public function getFields()
    {
        if (!$this->_fields) {
            $this->_fields = [];
            foreach ($this->getFieldset()->getAttributes() as $attribute) {
                if (!$attribute->getData('is_used_in_registration_form')) {
                    continue;
                }
                
                $attributeCode = $attribute->getAttributeCode();
                $renderers = $this->getFieldRenderers();
                if(isset($renderers[$attributeCode])){
                    $field = $this->getLayout()->createBlock($renderers[$attributeCode])->setVendorAttribute($attribute);
                }elseif ($attributeCode == 'vendor_id') {
                    $field = $this->getLayout()->createBlock('Vnecoms\Vendors\Block\Account\Create\Fieldset\VendorId')
                        ->setVendorAttribute($attribute)
                        ->setTemplate('account/create/fieldset/vendor_id.phtml');
                }elseif ($attributeCode == 'region') {
                    $field = $this->getLayout()->createBlock('Vnecoms\Vendors\Block\Account\Create\Fieldset\Region')
                        ->setVendorAttribute($attribute)
                        ->setTemplate('account/create/fieldset/region.phtml');
                } elseif ($attributeCode == 'region_id') {
                    continue;
                } else {
                    $field = $this->getLayout()->createBlock('Vnecoms\Vendors\Block\Account\Create\Fieldset\Field')
                        ->setVendorAttribute($attribute);
                }
                
                $this->_fields[] = $field;
            }
        }
        return $this->_fields;
    }
    
    /**
     * Get field renderers
     * 
     * @return array
     */
    public function getFieldRenderers(){
        $transport = new \Magento\Framework\DataObject(['renderers' => []]);
        $this->_eventManager->dispatch('vnecoms_vendors_register_field_renders',['transport' => $transport]);
        
        return $transport->getRenderers();
    }
    
    /**
     * return null if the fieldset object is not set.
     * @see \Magento\Framework\View\Element\Template::_toHtml()
     */
    protected function _toHtml()
    {
        if (!$this->getFieldset()) {
            return '';
        }
        return parent::_toHtml();
    }
}
