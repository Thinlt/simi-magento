<?php

/**
 * Connector data helper
 */

namespace Simi\Simicustomize\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Simi\Simiconnector\Helper\Data
{
    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }
}
