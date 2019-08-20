<?php

namespace Vnecoms\VendorsProduct\Model\Source\Product;

class Attributes extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    protected $attrCollection;
    
    /**
     * Options array
     *
     * @var array
     */
    protected $_options = null;
    
    /**
     * @var int
     */
    protected $_entityTypeId = null;
    
    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $collection
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $collection
    ) {
        $this->attrCollection = $collection;
        $this->attrCollection->addVisibleFilter();
    }
    
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            foreach ($this->attrCollection as $attr) {
                $this->_options[] = ['label' => $attr->getFrontendLabel(), 'value' => $attr->getAttributeCode()];
            }
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
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
