<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\Source\Product;

class Set extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Grid\Collection
     */
    protected $_setCollection;
    
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
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Grid\Collection $setCollection
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $setCollection
    ) {
        $this->_entityTypeId = $productFactory->create()->getResource()->getTypeId();
        $this->_setCollection = $setCollection;
        $this->_setCollection->setEntityTypeFilter($this->_entityTypeId);
    }
    
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            foreach ($this->_setCollection as $set) {
                $this->_options[] = ['label' => $set->getAttributeSetName(), 'value' => $set->getId()];
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
