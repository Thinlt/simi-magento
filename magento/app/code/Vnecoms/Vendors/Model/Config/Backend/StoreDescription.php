<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\Config\Backend;

/**
 * System config file field backend model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class StoreDescription extends \Vnecoms\VendorsConfig\Model\Config
{
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Vnecoms\VendorsConfig\Model\ResourceModel\Config $resource
     * @param \Vnecoms\VendorsConfig\Model\ResourceModel\Config\Collection $resourceCollection
     * @param \Vnecoms\VendorsConfig\Helper\Data $configHelper
     * @param \Vnecoms\Vendors\Helper\Data $vendorHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Vnecoms\VendorsConfig\Model\ResourceModel\Config $resource = null,
        \Vnecoms\VendorsConfig\Model\ResourceModel\Config\Collection $resourceCollection = null,
        \Vnecoms\VendorsConfig\Helper\Data $configHelper,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        array $data = []
    ) {
        $this->_vendorHelper = $vendorHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $configHelper, $data);
    }

    /**
     * Save uploaded file before saving config value
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        
        $length = $this->_vendorHelper->getDescriptionMaxLength();
        if (strlen($value) > $length) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Short Description must be less than %1 characters', $length)
            );
        }
        return $this;
    }
}
