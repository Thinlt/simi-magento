<?php

namespace Simi\VendorMapping\Plugin\Carrier;

use Magento\Shipping\Model\Rate\Result as RateResult;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Aramex\Shipping\Model\Aramex as AramexCarrier;

class Aramex
{
    /**
     * Copied from Vnecoms\VendorsShipping\Plugin\Shipping
     * Characters between methods */
    const METHOD_SEPARATOR = '|_|';

    /**
     * Object of \Magento\Store\Model\ScopeInterface
     * @var \Magento\Store\Model\ScopeInterface
     */
    const SCOPE_STORE = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    /** \Magento\Framework\App\Config */
    protected $_configFactory;

    /** \Vnecoms\Vendors\Model\VendorFactory */
    protected $_vendorFactory;

    public function __construct(
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory,
        \Magento\Framework\App\ConfigFactory $config
    )
    {
        $this->_vendorFactory = $vendorFactory;
        $this->_configFactory = $config;
    }

    /**
     * Set vendor_id default to items
     */
    // public function beforeCollectRates(
    //     AramexCarrier $subject,
    //     RateRequest $request
    // ){
        
    // }

    public function around_getAramexQuotes(
        AramexCarrier $subject,
        \Closure $proceed
    ){
        return $proceed();

        $r = $subject->_rawRequest;
        $pkgWeight = $r->getWeightPounds();
        $pkgQty = $r->getPackageQty();
        $product_group = 'EXP';
        $allowed_methods_key = 'allowed_international_methods';

        $allowed_methods = $subject->internationalmethods->toKeyArray();
        if ($subject->_scopeConfig->getValue('aramex/shipperdetail/country', self::SCOPE_STORE) == $r->
            getDestCountryId()) {
            $product_group = 'DOM';
            $allowed_methods = $subject->domesticmethods->toKeyArray();
            $allowed_methods_key = 'allowed_domestic_methods';
        }
        $admin_allowed_methods = explode(',', $subject->getConfigData($allowed_methods_key));
        $admin_allowed_methods = array_flip($admin_allowed_methods);
        $allowed_methods = array_intersect_key($allowed_methods, $admin_allowed_methods);

        $OriginAddress = [
            'StateOrProvinceCode' => $subject->_scopeConfig->getValue('aramex/shipperdetail/state', self::SCOPE_STORE),
            'City' => $subject->_scopeConfig->getValue('aramex/shipperdetail/city', self::SCOPE_STORE),
            'PostCode' => $subject->_scopeConfig->getValue('aramex/shipperdetail/postalcode', self::SCOPE_STORE),
            'CountryCode' => $subject->_scopeConfig->getValue('aramex/shipperdetail/country', self::SCOPE_STORE),
        ];
        $DestinationAddress = [
            'StateOrProvinceCode' => $r->getDestState(),
            'City' => $r->getDestCity(),
            'PostCode' => $subject::USA_COUNTRY_ID == $r->getDestCountryId() ? substr($r->getDestPostal(), 0, 5) : $r->
            getDestPostal(),
            'CountryCode' => $r->getDestCountryId(),
        ];
        $ShipmentDetails = [
            'PaymentType' => 'P',
            'ProductGroup' => $product_group,
            'ProductType' => '',
            'ActualWeight' => ['Value' => $pkgWeight, 'Unit' => 'KG'],
            'ChargeableWeight' => ['Value' => $pkgWeight, 'Unit' => 'KG'],
            'NumberOfPieces' => $pkgQty
        ];
        //city = NULL fixing
        $city_from_base = "";
        $customerSession = $subject->sessionCustomer;
        if ($customerSession->isLoggedIn()) {
            $customerObj = $subject->customer->load($customerSession->getCustomer()->getId());
            $customerAddress = [];
            foreach ($customerObj->getAddresses() as $address) {
                $customerAddress[] = $address->toArray();
            }
            foreach ($customerAddress as $customerAddres) {
                if ($customerAddres['postcode'] == $r->getDestPostal()) {
                    $city_from_base =  $customerAddres['city'];
                }
            }
            if (!empty($customerAddress)) {
                $DestinationAddress['City'] = $city_from_base;
            }
        }
        $clientInfo = $subject->helper->getClientInfo();
        $baseCurrencyCode = $subject->storeManager->getStore()->getBaseCurrency()->getCode();
        $params = [
            'ClientInfo' => $clientInfo,
            'OriginAddress' => $OriginAddress,
            'DestinationAddress' => $DestinationAddress,
            'ShipmentDetails' => $ShipmentDetails,
            'PreferredCurrencyCode' => $baseCurrencyCode
            ];
        $priceArr = [];
		$requestFromAramex = [];
        foreach ($allowed_methods as $m_value => $m_title) {
            $params['ShipmentDetails']['ProductType'] = $m_value;
            if ($m_value == "CDA") {
                $params['ShipmentDetails']['Services'] = "CODS";
            } else {
                $params['ShipmentDetails']['Services'] = "";
            }
            $requestFromAramex = $subject->makeRequestToAramex($params, $m_value, $m_title);
            if (isset($requestFromAramex['response']['error'])) {
				continue;
            }
            $priceArr[] = $requestFromAramex['priceArr'];
        }

        $result = $subject->sendResult($priceArr, $requestFromAramex);
        return $result;
    }

    /**
     * @param RateRequest $request
     * @return RateResult|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function afterCollectRates(
        AramexCarrier $subject,
        RateResult $result,
        RateRequest $request
    ){
        return $result;
        /* getAllRates: Magento\Quote\Model\Quote\Address\RateResult\Method[] */
        $rateResultMethods = $result->getAllRates();
        $result->reset();
        // $rateAdded = [];

        // get all vendor ids from quote items
        $allVendors = []; //make default vendor for product has no vendor
        $allVendorIds = []; //make default vendor for product has no vendor
        $vendor = $this->_vendorFactory->create(); //Vnecoms\Vendors\Model\Vendor
        $vendor->setVendorId('default');
        $allVendors[] = $vendor;
        foreach ($request->getAllItems() as $item) {
            $vendor_id = $item->getVendorId();
            if ($vendor_id && $vendor_id != 'default' && !isset($allVendorIds[$vendor_id])) {
                $vendorLoaded = $this->_vendorFactory->create()->loadByVendorId($vendor_id); //Vnecoms\Vendors\Model\Vendor
                $allVendors[] = $vendorLoaded;
            }
        }
        // set for each vendor (id) in current quote items
        foreach ($allVendors as $vendor) {
            foreach($rateResultMethods as $code => $rate){
                // $code = $method->getCarrier().'_'.rtrim($method->getMethod(), $method->getVendorId());
                $rate->setVendorId($vendor->getVendorId());
                $vendorEntityId = $vendor->getId() ? $vendor->getId() : '0';
                $rate->setMethod($rate->getMethod().$code.self::METHOD_SEPARATOR.$vendor->getVendorId().'_'.$vendorEntityId);
                $result->append($rate);
            }
        }

        // $method->setCarrier($this->_code);
        // $method->setVendorId($vendorId);
        // $method->setCarrierTitle($this->getConfigData('title'));
        // $method->setMethodTitle($rate['title']);
        // $method->setMethod($code.\Vnecoms\VendorsShipping\Plugin\Shipping::SEPARATOR.$vendorId);

        
        return $result;
    }
}