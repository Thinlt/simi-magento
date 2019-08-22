<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Plugin;
use Magento\Sales\Model\Order\Shipment;

class Shipping
{
    /*Characters between method and vendor_id*/
    const SEPARATOR = '||';

    /*Characters between methods*/
    const METHOD_SEPARATOR = '|_|';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var \Vnecoms\VendorsShipping\Helper\Data
     */
    protected $helper;
    
    /**
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Vnecoms\VendorsShipping\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\VendorsShipping\Helper\Data $helper,
        array $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->helper = $helper;
    }
    /**
     * Collect rates
     *
     * @param \Magento\Shipping\Model\Shipping $subject
     * @param \Magento\Shipping\Model\Shipping $result
     * @return \Magento\Shipping\Model\Shipping
     */
    public function afterCollectRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Magento\Shipping\Model\Shipping $result
    ) {
        if(!$this->helper->isEnabled()) return $result;
        $shippingRates = $subject->getResult()->getAllRates();
        $newVendorRates = [];
        foreach ($this->groupShippingRatesByVendor($shippingRates) as $vendorId => $rates) {
            if (!sizeof($newVendorRates)) {
                foreach ($rates as $rate) {
                    $newVendorRates[$rate->getCarrier().'_'.$rate->getMethod()] = [
                        'title' => $rate->getCarrierTitle().' - '.$rate->getMethodTitle().self::SEPARATOR.$vendorId,
                        'price' => $rate->getPrice()
                    ];
                }
            } else {
                $tmpRates = [];
                foreach ($rates as $rate) {
                    foreach ($newVendorRates as $cod => $shipping) {
                        $tmpRates[$cod.self::METHOD_SEPARATOR.$rate->getCarrier().'_'.$rate->getMethod()] = [
                            'title' => $shipping['title'].self::METHOD_SEPARATOR.$rate->getCarrierTitle().' - '.$rate->getMethodTitle().self::SEPARATOR.$vendorId,
                            'price' => $shipping['price']+$rate->getPrice(),
                        ];
                    }
                }
                $newVendorRates = $tmpRates;
            }
        }
        foreach ($newVendorRates as $code => $shipping) {
            $method = $this->_rateMethodFactory->create();
            $method->setCarrier('vendor_multirate');
            $method->setCarrierTitle('Multiple_Rate');

            $method->setMethod($code);
            $method->setMethodTitle($shipping['title']);

            $method->setPrice($shipping['price']);
            $method->setCost($shipping['price']);
            $subject->getResult()->append($method);
        }

        return $result;
    }

    /**
     * Group shipping rates by each vendor.
     * @param unknown $shippingRates
     */
    public function groupShippingRatesByVendor($shippingRates)
    {
        $rates = [];
        foreach ($shippingRates as $rate) {
            if (!$rate->getVendorId()) {
                continue;
            }
            if (!isset($rates[$rate->getVendorId()])) {
                $rates[$rate->getVendorId()] = [];
            }
            $rates[$rate->getVendorId()][] = $rate;
        }
        ksort($rates);
        return $rates;
    }


    /**
     * Retrieve all methods for supplied shipping data
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return $this
     * @todo make it ordered
     */
    public function aroundCollectRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ) {

        $storeId = $request->getStoreId();
        if (!$request->getOrig()) {
            $request->setCountryId(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_COUNTRY_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            )->setRegionId(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_REGION_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            )->setCity(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_CITY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            )->setPostcode(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_ZIP,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            );
        }

        $carriers = $this->_scopeConfig->getValue(
            'carriers',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        foreach ($carriers as $carrierCode => $carrierConfig) {
            $subject->collectCarrierRates($carrierCode, $request);
        }

        return $subject;
    }
}
