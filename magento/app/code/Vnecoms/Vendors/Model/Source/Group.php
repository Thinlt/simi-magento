<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\Source;

class Group extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Options array
     *
     * @var array
     */
    protected $_options = null;
    
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions($blankOption = true)
    {
        if ($this->_options === null) {
            $this->_options = [];
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            if ($blankOption) {
                $this->_options[] = ['label' => __("-- Please Select --"), 'value' => ''];
            }
            $vendorGroupCollection = $om->create('Vnecoms\Vendors\Model\Group')->getCollection();
            foreach ($vendorGroupCollection as $group) {
                $this->_options[] = ['label' => $group->getVendorGroupCode(), 'value' => $group->getId()];
            }
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray($blankOption = true)
    {
        $_options = [];
        foreach ($this->getAllOptions($blankOption) as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
    
    
    /**
     * Get options as array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
