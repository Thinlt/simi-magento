<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\Vendor;

class Fieldset extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    
    protected $_eventPrefix = 'vendor_fieldset';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'vendor_fieldset';
    
    /**
     * The list of attributes in the fieldset
     * @var array
     */
    protected $_attributes;
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Vendors\Model\ResourceModel\Vendor\Fieldset');
    }
    
    /**
     * Get the list of attributes in the fieldset.
     * @return array
     */
    public function getAttributes()
    {
        if (!$this->_attributes) {
            $this->_attributes = $this->getResource()->getAttributes($this);
        }
        
        return $this->_attributes;
    }
    
    /**
     * Save attributes
     * @param array $attributes
     */
    public function saveAttributes($attributes = [])
    {
        return $this->getResource()->saveAttributes($this, $attributes);
    }
}
