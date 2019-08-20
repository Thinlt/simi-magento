<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\Entity\Attribute;

class Group extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    
    protected $_eventPrefix = 'vendor_product_attribute_group';
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsProduct\Model\ResourceModel\Entity\Attribute\Group');
    }
    
    
    /**
     * Checks if current attribute group exists
     *
     * @return boolean
     */
    public function itemExists()
    {
        return $this->_getResource()->itemExists($this);
    }
    
    /**
     * Get Attribute Group Name
     * @return string
     */
    public function getAttributeGroupName()
    {
        return $this->getName();
    }

    /**
     * Get Attribute Group Id
     * @return string
     */
    public function getAttributeGroupId()
    {
        return $this->getGroupId();
    }
    
    /**
     * @return string
     */
    public function getAttributeGroupCode()
    {
        if (!$this->getData('attribute_group_code')) {
            $groupName = strtolower($this->getAttributeGroupName());
            if ($groupName) {
                $attributeGroupCode = trim(
                    preg_replace(
                        '/[^a-z0-9]+/',
                        '-',
                        $groupName
                    ),
                    '-'
                );
                if (empty($attributeGroupCode)) {
                    // in the following code md5 is not used for security purposes
                    $attributeGroupCode = md5($groupName);
                }
            }
            
            return $attributeGroupCode;
        }
    }
}
