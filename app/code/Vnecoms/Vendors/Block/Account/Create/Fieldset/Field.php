<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Account\Create\Fieldset;

/**
 * Widget for showing customer name.
 *
 * @method CustomerInterface getObject()
 * @method Name setObject(CustomerInterface $customer)
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Field extends AbstractWidget
{
    /**
     * Class name getter
     *
     * @return string
     */
    public function getFrontendClass()
    {
        if (!$this->hasData('frontend_class')) {
            $class = [
                'input-text',
                'vendor-field',
                $this->_attribute->getAttributeCode(),
                $this->_attribute->getFrontendClass(),
            ];
            
            if ($this->isAttributeRequired()) {
                $class[] = 'required-entry';
            }
            $class = implode(' ', $class);
            $this->setData('frontend_class', $class);
        }
        return $this->getData('frontend_class');
    }

    /**
     * Retrieve store attribute label
     *
     * @return string
     */
    public function getStoreLabel()
    {
        return $this->_attribute->getStoreLabel();
    }
    
    /**
     * @return bool
     */
    public function isAttributeRequired()
    {
        return $this->_attribute->getIsRequired();
    }

    /**
     * @return bool
     */
    public function isAttributeVisible($attributeCode)
    {
        return $this->_attribute->getData('is_used_in_registration_form');
    }
    
    /**
     * Get options
     * @return multitype:
     */
    public function getOptions()
    {
        return $this->_attribute->getSource()->getAllOptions();
    }
}
