<?php

namespace Vnecoms\VendorsConfigApproval\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Data extends AbstractHelper
{
    const XML_PATH_CONFIG_RESTRICTION = 'vendors/vendor_config/restriction';
    
    /**
     * Get fields restriction
     * 
     * @return array
     */
    public function getFieldsRestriction(){
        $fields = explode(",", $this->scopeConfig->getValue(self::XML_PATH_CONFIG_RESTRICTION));
        return $fields;
    }
}
