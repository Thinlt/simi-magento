<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block;

use Magento\Customer\Model\Context;

/**
 * Shopping cart block
 */
class Cart extends \Magento\Checkout\Block\Cart
{
    /**
     * Group Items by vendor
     * @return Ambigous <multitype:multitype: , unknown>
     */
    public function groupItemsByVendor(){
        $quotes = array();
        $quoteItems = $this->getItems();
        foreach($quoteItems as $item) {
            $product    = $item->getProduct()->load($item->getProductId());
            if($item->getProduct()->getVendorId()) {
                if($item->getVendorId()){
                    $vendorId = $item->getVendorId();
                }else{
                    $vendorId = $item->getProduct()->getVendorId();
                }
                $om  = \Magento\Framework\App\ObjectManager::getInstance();
                $transport = new \Magento\Framework\DataObject(array('vendor_id'=>$vendorId,'item'=>$item));
                $eventManager = $om->create('\Magento\Framework\Event\ManagerInterface');

                $eventManager->dispatch('ves_vendors_checkout_init_vendor_id',['transport' => $transport]);

                $vendorId = $transport->getVendorId();

                /*Get item by vendor id*/
                if(!isset($quotes[$vendorId])) $quotes[$vendorId] = [];
                $quotes[$vendorId][] = $item;
            } else {
                $quotes['no_vendor'][] = $item;
            }
        }
        return $quotes;
    }

    /**
     * is split cart
     * @return mixed
     */
    public function isSliptCart(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helperFactory = $objectManager->create('\Vnecoms\VendorsSales\Helper\Data');
        return $helperFactory->isSplitCartByVendor();
    }

    /**
     * get vendor name by vendor Id
     * @param $vendorId
     * @return mixed
     */

    public function getVendorName($vendorId){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $modelFactory = $objectManager->create('\Vnecoms\Vendors\Model\Vendor')->load($vendorId);
        if(!$vendorId || !$modelFactory->getId()) return __("No Vendor");
        return $modelFactory->getVendorId();
    }

    /**
     * @param $vendorId
     * @return bool
     */
    public function getUrlVendorPage($vendorId){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $module = $objectManager->create('Magento\Framework\Module\Manager');
        if(!$module->isEnabled("Vnecoms_VendorsPage")) return false;
        $modelFactory = $objectManager->create('\Vnecoms\Vendors\Model\Vendor')->load($vendorId);
        if(!$vendorId || !$modelFactory->getId()) return false;
        $url = $objectManager->create('\Vnecoms\VendorsPage\Helper\Data')->getUrl($modelFactory);
        return $url;
    }
}
