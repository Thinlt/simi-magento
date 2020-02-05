<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProductConfigurable\Model\Product\AttributeSet;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var null|array
     */
    protected $options;

    /**
     * @var \Vnecoms\VendorsProduct\Helper\Data
     */
    protected $_vendorProductHelper;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $product
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $product,
        \Vnecoms\VendorsProduct\Helper\Data $helper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->product = $product;
        $this->_vendorProductHelper =  $helper;
    }

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (null == $this->options) {
            $this->options = $this->collectionFactory->create()
                ->setEntityTypeFilter($this->product->getTypeId())
                ->addFieldToFilter('attribute_set_id', ['nin'=>$this->_vendorProductHelper->getAttributeSetRestriction()])
                ->toOptionArray();
        }
        return $this->options;
    }
}
