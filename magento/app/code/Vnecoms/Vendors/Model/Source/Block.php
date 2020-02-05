<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\Source;

class Block extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
            $blockCollection = $om->create('Magento\Cms\Model\Block')->getCollection();
            foreach ($blockCollection as $block) {
                $this->_options[] = ['label' => $block->getTitle(), 'value' => $block->getId()];
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
