<?php

namespace Vnecoms\VendorsConfig\Block\Store;

class Switcher extends \Magento\Backend\Block\Store\Switcher
{
    /**
     * Get websites
     *
     * @return \Magento\Store\Model\Website[]
     */
    public function getWebsites()
    {
        $websites = [$this->_storeManager->getWebsite()];
        return $websites;
    }
}
