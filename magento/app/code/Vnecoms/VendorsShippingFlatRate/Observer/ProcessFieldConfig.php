<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShippingFlatRate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsConfig\Helper\Data;

class ProcessFieldConfig implements ObserverInterface
{
    /**
     * Add multiple vendor order row for each vendor.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $transport = $observer->getTransport();
        $fieldset = $transport->getFieldset();

        $config = \Magento\Framework\App\ObjectManager::getInstance()->get(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        );

        if ($fieldset->getHtmlId() == "shipping_method_flatrate") {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
            $configVal = $config->getValue("carriers/vflatrate/active", $storeScope);
            $configShipping = $config->getValue("carriers/vendor_multirate/active", $storeScope);
            if (!$configVal || !$configShipping) {
                $transport->setForceReturn(true);
            }
        }

        return $this;
    }
}
