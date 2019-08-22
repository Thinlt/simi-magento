<?php

namespace Vnecoms\VendorsShipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Data extends AbstractHelper
{
    
    public function isEnabled()
    {
        return $this->scopeConfig->getValue('carriers/vendor_multirate/active');
    }
}
