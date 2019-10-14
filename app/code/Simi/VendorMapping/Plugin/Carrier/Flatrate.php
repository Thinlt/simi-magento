<?php

namespace Simi\VendorMapping\Plugin\Carrier;

use Magento\Shipping\Model\Rate\Result;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Vnecoms\VendorsShippingFlatRate\Model\Carrier\Flatrate as VnecomsFlatrate;
/**
 * Plugin of Vnecoms\VendorsShippingFlatRate\Model\Carrier\Flatrate
 */
class Flatrate
{
    /** \Magento\Framework\App\Config */
    protected $_configFactory;

    public function __construct(
        \Magento\Framework\App\ConfigFactory $config,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory
    )
    {
        $this->_configFactory = $config;
        $this->_vendorFactory = $vendorFactory;
    }

    /**
     * Custom get flatrate from system configuration table instead of vnecoms vendor flatrate table
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @param   string $vendor_id
     * @return  mixed
     */
    public function afterGetVendorConfigData(
        VnecomsFlatrate $subject,
        $result,
        $field, $vendorId
    ){
        if ($field == 'active') {
            return true;
        }
        if ($field == 'rates') {
            $config = $this->_configFactory->create();
            $configData = $config->getValue('shipping_method/flatrate/rates');
            $configFlatrate = [];
            if ($configData) {
                $configFlatrate = json_decode($configData, true);
            }
            $rates = [];
            foreach($configFlatrate as $row){
                $rates[$row['sort_order']] = [
                    'identifier' => $row['identifier'],
                    'title' => isset($row['title'])?$row['title']:'',
                    'type' => isset($row['type'])?$row['type']:'',
                    'price' => isset($row['price'])?$row['price']:0,
                    'free_shipping' => isset($row['free_shipping'])?$row['free_shipping']:'',
                    'sort_order' => isset($row['sort_order'])?$row['sort_order']:'',
                ];
            }
            ksort($rates);
            return serialize($rates);
        }
        return $result;
    }


    /**
     * Set vendor_id default to items
     */
    public function beforeCollectRates(
        VnecomsFlatrate $subject,
        RateRequest $request
    ){
        // fixbug: vender_id loaded when product load
        //update vendor id for item in quote
        // $allItems = [];
        // foreach ($request->getAllItems() as $item) {
        //     if ($item->getVendorId() == '' || (int)$item->getVendorId() == 0 || (int)$item->getIsAdminSell() == 1) {
        //         $item->setVendorId('default'); //set vendor_id for items that are does not belong to a vendor
        //         $product = $item->getProduct()->load($item->getProductId());
        //         $product->setVendorId('default');
        //         $item->setProduct($product);
        //     }
        //     $allItems[] = $item;
        // }
        // $request->setAllItems($allItems);

        return [$request];
    }

    /**
     * @param RateRequest $request
     * @return Result|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function afterCollectRates(
        VnecomsFlatrate $subject,
        Result $result,
        RateRequest $request
    ){
        //check vendor allowed by country, related to checkAvailableShipCountries()
        $speCountriesAllow = $subject->getConfigData('sallowspecific');
        if ($speCountriesAllow && $speCountriesAllow == 1) {
            $availableCountries = [];
            if ($subject->getConfigData('specificcountry')) {
                $availableCountries = explode(',', $subject->getConfigData('specificcountry'));
            }
            if (!empty($availableCountries)) {
                $rateMethods = $result->getAllRates();
                $result->reset();
                foreach($rateMethods as $method){
                    if ($method->getVendorId() == 'default') {
                        $result->append($method);
                        continue; // allowed flatrate for admin
                    }
                    if ($method->getVendorId() && $method->getVendorId() != 'default') {
                        $vendor = $this->_vendorFactory->create()->load($method->getVendorId()); //Vnecoms\Vendors\Model\Vendor
                        if ($vendor && in_array($vendor->getCountry(), $availableCountries)) {
                            $result->append($method);
                        }
                    }
                }
            }
        }
        return $result;
    }
}