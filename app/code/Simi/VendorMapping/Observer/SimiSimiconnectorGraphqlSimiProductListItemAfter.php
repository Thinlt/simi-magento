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
                if ($vendorId = $vendor->getId()) {
                    // productExtraData
                    $vendorHelper = \Magento\Framework\App\ObjectManager::getInstance()
                        ->get(\Simi\Simicustomize\Helper\Vendor::class);
                    $profile = $vendorHelper->getProfile($vendorId);
                    $object->productExtraData['attribute_values']['vendor_name'] =
                        ($profile && isset($profile['store_name']) && $profile['store_name']) ? $profile['store_name'] : $vendor->getName();
                }
            }
        }
        return $this;
    }
}