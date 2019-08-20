<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsLanguage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Data extends AbstractHelper
{
    const XML_SET_DEFAULT_LANGUAGE_VENDOR  = 'vendors/design/default_language';

    /**
     * @return mixed
     */
    public function getDefaultLanguageVendor()
    {
        return $this->scopeConfig->getValue(self::XML_SET_DEFAULT_LANGUAGE_VENDOR);
    }
}
