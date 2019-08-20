<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsConfig\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * @method int getCustomerId();
 * @method int getCredit();
 */
class Config extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'vendor_config';
    
    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_configHelper;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Vnecoms\VendorsConfig\Model\ResourceModel\Config $resource
     * @param \Vnecoms\VendorsConfig\Model\ResourceModel\Config\Collection $resourceCollection
     * @param \Vnecoms\VendorsConfig\Helper\Data $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Vnecoms\VendorsConfig\Model\ResourceModel\Config $resource = null,
        \Vnecoms\VendorsConfig\Model\ResourceModel\Config\Collection $resourceCollection = null,
        \Vnecoms\VendorsConfig\Helper\Data $configHelper,
        array $data = []
    ) {
        $this->_configHelper = $configHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsConfig\Model\ResourceModel\Config');
    }
}
