<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\Source\Product;

class Type extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    
    /**
     * @var \Magento\Catalog\Model\Product\TypeFactory
     */
    protected $_typeFactory;
    
    /**
     * Options array
     *
     * @var array
     */
    protected $_options = null;
    
    /**
     * @param \Magento\Catalog\Model\Product\TypeFactory $typeFactory
     */
    public function __construct(
        \Magento\Catalog\Model\Product\TypeFactory $typeFactory
    ) {
        $this->_typeFactory = $typeFactory;
    }
    
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $types = $this->_typeFactory->create()->getTypes();
            uasort(
                $types,
                function ($elementOne, $elementTwo) {
                    return ($elementOne['sort_order'] < $elementTwo['sort_order']) ? -1 : 1;
                }
            );
            foreach ($types as $typeId => $type) {
                $this->_options[] = ['label' => $type['label'], 'value' => $typeId];
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
