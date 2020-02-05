<?php

namespace Vnecoms\VendorsConfigApproval\Model;

class Config extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_PENDING    = 0;
    const STATUS_REJECTED   = 1;
    
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'vendor_config_approval';
    
    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_configHelper;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\VendorsConfigApproval\Model\ResourceModel\Config');
    }
}
