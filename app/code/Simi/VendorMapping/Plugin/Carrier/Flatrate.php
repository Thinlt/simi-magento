<?php

namespace Simi\VendorMapping\Plugin\Carrier;

use Magento\Shipping\Model\Rate\Result;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Vnecoms\VendorsShippingFlatRate\Model\Carrier\Flatrate as VnecomsFlatrate;

class Flatrate
{
    /** \Magento\Framework\App\Config */
    protected $_configFactory;

    public function __construct(
        \Magento\Framework\App\ConfigFactory $config
    )
    {
        $this->_configFactory = $config;
    }

    /**
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
            $configData = json_decode($configData, true);
            $rates = [];
            foreach($configData as $row){
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
        // $rateMethods = $result->getAllRates();
        // $result->reset();
        // $methodAdded = [];
        // foreach($rateMethods as $method){
        //     $code = $method->getCarrier().'_'.rtrim($method->getMethod(), $method->getVendorId());
        //     if (!in_array($code, $methodAdded)) {
        //         $methodAdded[] = $code;
        //         $method->setVendorId('');
        //         $method->setMethod($code.'0'); //remove vendor id => 0
        //         $result->append($method);
        //     }
        // }
        return $result;
    }
}