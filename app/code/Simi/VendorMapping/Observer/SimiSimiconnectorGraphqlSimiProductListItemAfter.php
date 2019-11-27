<?php

namespace Simi\VendorMapping\Observer;

use Magento\Framework\Event\ObserverInterface;

class SimiSimiconnectorGraphqlSimiProductListItemAfter implements ObserverInterface {

    /**
     * Add vendor_name to SimiProductListItemExtraField
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $object = $observer->getObject();
        // $extraData = $observer->getExtraData();
        if (isset($object->productExtraData['attribute_values']['vendor_id'])) {
            if (class_exists('Vnecoms\Vendors\Model\Vendor')) {
                $vendor = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get(\Vnecoms\Vendors\Model\Vendor::class)
                    ->load($object->productExtraData['attribute_values']['vendor_id']);
                if ($vendor->getId()) {
                    $vendor_name = $vendor->getName();
                    // productExtraData
                    $object->productExtraData['attribute_values']['vendor_name'] = $vendor_name;
                }
            }
        }
        return $this;
    }
}