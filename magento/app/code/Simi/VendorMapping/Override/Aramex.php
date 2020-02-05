<?php

namespace Simi\VendorMapping\Override;

// use Magento\Shipping\Model\Rate\Result as RateResult;
// use Magento\Quote\Model\Quote\Address\RateRequest;
// use Aramex\Shipping\Model\Aramex as AramexCarrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\Xml\Security;
use \Magento\Customer\Model\Session;

class Aramex extends AbstractCarrierOnline implements CarrierInterface
{
    /*Characters between method and vendor_id*/
    const SEPARATOR = '||';

    /**
     * Copied from Vnecoms\VendorsShipping\Plugin\Shipping
     * Characters between methods */
    const METHOD_SEPARATOR = '|_|';

    /**
     * Object of \Aramex\Shipping\Model\Carrier\Aramex\Source\Domesticmethods
     * @var \Aramex\Shipping\Model\Carrier\Aramex\Source\Domesticmethods
     */
    private $domesticmethods;
    
    /**
     * object \Aramex\Shipping\Model\Carrier\Aramex\Source\Internationalmethods
     * @var \Aramex\Shipping\Model\Carrier\Aramex\Source\Internationalmethods
     */
    private $internationalmethods;
    
    /**
     * Object of \Magento\Store\Model\ScopeInterface
     * @var \Magento\Store\Model\ScopeInterface
     */
    const SCOPE_STORE = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    
    /**
     * Request
     * @var array
     */
    private $request;
    
    /**
     * Result
     * @var array
     */
    private $result;
    
    /**
     * Local format
     * @var string
     */
    private $localeFormat;
    
    /**
     * Array of errors
     * @var array
     */
    private $errors = [];
    
    /**
     * Default Gateway Url
     * @var string
     */
    private $defaultGatewayUrl = null;
    
    /**
     * Object of \Aramex\Shipping\Helper\Data
     * @var \Aramex\Shipping\Helper\Data
     */
    private $helper;
    
    /**
     * Object of \Magento\Store\Model\StoreManagerInterface
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * Object of \Magento\Customer\Model\Customer
     * @var \Magento\Customer\Model\Customer
     */
    private $customer;
    
    /**
     * Object of \Magento\Directory\Model\Config\Source\Country
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    private $country;
    
    /**
     * Object of \Magento\Customer\Model\Session
     * @var \Magento\Customer\Model\Session
     */
    private $sessionCustomer;
    /**
     * @var \Magento\Framework\Webapi\Soap\ClientFactory
     */
    private $soapClientFactory;

    private $objectFactory;

    /** \Vnecoms\Vendors\Model\VendorFactory */
    protected $_vendorFactory;

    protected $_fromspecificcountry; //scope config carriers/aramex/fromspecificcountry
    protected $_sallowfromspecific; //scope config carriers/aramex/sallowfromspecific

     /**
      * Constructor
      *
      * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
      * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
      * @param \Aramex\Shipping\Helper\Data $helper
      * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
      * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
      * @param \Psr\Log\LoggerInterface $logger
      * @param \Magento\Framework\Xml\Security $xmlSecurity
      * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
      * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
      * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
      * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
      * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
      * @param \Magento\Directory\Model\RegionFactory $regionFactory
      * @param \Magento\Directory\Model\CountryFactory $countryFactory
      * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
      * @param \Magento\Directory\Helper\Data $directoryData
      * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
      * @param \Magento\Framework\Locale\FormatInterface $localeFormat
      * @param \Magento\Store\Model\StoreManagerInterface $storeManager
      * @param \Aramex\Shipping\Model\Carrier\Aramex\Source\Domesticmethods $domesticmethods
      * @param \Aramex\Shipping\Model\Carrier\Aramex\Source\Internationalmethods $internationalmethods
      * @param \Magento\Customer\Model\Customer $customer
      * @param \Magento\Directory\Model\Config\Source\Country $country
      * @param \Magento\Customer\Model\Session $sessionCustomer
      * @param array Data
      */
    
    public function __construct(
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Aramex\Shipping\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Aramex\Shipping\Model\Carrier\Aramex\Source\Domesticmethods $domesticmethods,
        \Aramex\Shipping\Model\Carrier\Aramex\Source\Internationalmethods $internationalmethods,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Directory\Model\Config\Source\Country $country,
        Session $sessionCustomer,
        \Magento\Framework\Webapi\Soap\ClientFactory $soapClientFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->helper = $helper;
        $this->localeFormat = $localeFormat;
        $this->storeManager = $storeManager;
        $this->domesticmethods = $domesticmethods;
        $this->internationalmethods = $internationalmethods;
        $this->customer = $customer;
        $this->country = $country;
        $this->sessionCustomer= $sessionCustomer;
        $this->_code = $helper->getCode();
        $this->soapClientFactory = $soapClientFactory;
        $this->objectFactory = $objectFactory;
        $this->_vendorFactory = $vendorFactory;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
        $this->defaultGatewayUrl = $this->helper->getWsdlPath();
    }
    
    /**
     * Makes shipment request
     *
     * @param object $request Request
     * @return void
     */
    public function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $request = null;
        $this->defaultGatewayUrl = $this->helper->getWsdlPath() . 'Tracking.wsdl';
    }
    
    /**
     * Gets all allowed methods
     *
     * @return void
     */
    public function getAllowedMethods()
    {
        $this->defaultGatewayUrl = $this->helper->getWsdlPath() . 'Tracking.wsdl';
    }
    
    /**
     * Collects Rates
     *
     * @param object $request request
     * @return void|array Rates
     */
    public function collectRates(RateRequest $request)
    {
        $this->request = $request;
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $this->setRequest($request);
        return $this->result = $this->_getQuotes();
    }
    
    /**
     * Sets request
     *
     * @param object $request Request
     */
    public function setRequest(RateRequest $request)
    {
        $this->request = $request;
        $r = $this->objectFactory->create();
        $r = $this->setAdditionalData($request, $r);
        if ($request->getAramexMachinable()) {
            $machinable = $request->getAramexMachinable();
        } else {
            $machinable = $this->getConfigData('machinable');
        }
        $r->setMachinable($machinable);
        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal($this->_scopeConfig->getValue('shipping/origin/postcode', self::SCOPE_STORE) == 1);
        }

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }
        $r->setDestCountryId($destCountry);

        $countries = $this->country->toOptionArray();
        foreach ($countries as $country) {
            if ($country['value'] == $destCountry) {
                $r->setDestCountryName($country['label']);
            }
        }
        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        }
        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $r->setWeightPounds($weight);
        $r->setPackageQty($request->getPackageQty());
        $r->setWeightOunces(round(($weight - floor($weight)) * 16, 1));
        if ($request->getFreeMethodWeight() != $request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }
        $r->setDestState($request->getDestRegionCode());
        $r->setValue($request->getPackageValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());
        $r->setDestCity($request->getDestCity());
        $this->_rawRequest = $r;
    }
    
    /**
     * Sets additional data
     *
     * @param object $request Request
     * @param object $r Service
     * @return array Service
     */
    public function setAdditionalData($request, $r)
    {
        if ($request->getLimitMethod()) {
            $r->setService($request->getLimitMethod());
        } else {
            $r->setService('ALL');
        }

        if ($request->getAramexUserid()) {
            $userId = $request->getAramexUserid();
        } else {
            $userId = $this->getConfigData('userid');
        }
        $r->setUserId($userId);

        if ($request->getAramexContainer()) {
            $container = $request->getAramexContainer();
        } else {
            $container = $this->getConfigData('container');
        }
        $r->setContainer($container);

        if ($request->getAramexSize()) {
            $size = $request->getAramexSize();
        } else {
            $size = $this->getConfigData('size');
        }
        $r->setSize($size);
        return $r;
    }
    
    /**
     * Gets quotes
     *
     * @return array Quotes
     */
    private function _getQuotes()
    {
        return $this->_getAramexQuotes();
    }
    
    /**
     * Sets Aramex quote
     *
     * @return object Quote
     */
    public function _getAramexQuotes()
    {
        $r = $this->_rawRequest;
        $pkgWeight = $r->getWeightPounds();
        $pkgQty = $r->getPackageQty();
        $product_group = 'EXP';
        $allowed_methods_key = 'allowed_international_methods';

        $allowed_methods = $this->internationalmethods->toKeyArray();
        if ($this->_scopeConfig->getValue('aramex/shipperdetail/country', self::SCOPE_STORE) == $r->
            getDestCountryId()) {
            $product_group = 'DOM';
            $allowed_methods = $this->domesticmethods->toKeyArray();
            $allowed_methods_key = 'allowed_domestic_methods';
        }
        $admin_allowed_methods = explode(',', $this->getConfigData($allowed_methods_key));
        $admin_allowed_methods = array_flip($admin_allowed_methods);
        $allowed_methods = array_intersect_key($allowed_methods, $admin_allowed_methods);

        $OriginAddress = [
            'StateOrProvinceCode' => $this->_scopeConfig->getValue('aramex/shipperdetail/state', self::SCOPE_STORE),
            'City' => $this->_scopeConfig->getValue('aramex/shipperdetail/city', self::SCOPE_STORE),
            'PostCode' => $this->_scopeConfig->getValue('aramex/shipperdetail/postalcode', self::SCOPE_STORE),
            'CountryCode' => $this->_scopeConfig->getValue('aramex/shipperdetail/country', self::SCOPE_STORE),
        ];
        $DestinationAddress = [
            'StateOrProvinceCode' => $r->getDestState(),
            'City' => $r->getDestCity(),
            'PostCode' => self::USA_COUNTRY_ID == $r->getDestCountryId() ? substr($r->getDestPostal(), 0, 5) : $r->
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
        $customerSession = $this->sessionCustomer;
        if ($customerSession->isLoggedIn()) {
            $customerObj = $this->customer->load($customerSession->getCustomer()->getId());
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
        $clientInfo = $this->helper->getClientInfo();
        $baseCurrencyCode = $this->storeManager->getStore()->getBaseCurrency()->getCode();
        $params = [
            'ClientInfo' => $clientInfo,
            'OriginAddress' => $OriginAddress,
            'DestinationAddress' => $DestinationAddress,
            'ShipmentDetails' => $ShipmentDetails,
            'PreferredCurrencyCode' => $baseCurrencyCode
            ];
        
        // $paramsByVendor = [];
        // get all vendor ids from quote items
        $allVendors = []; //make default vendor for product has no vendor
        $allVendorIds = []; //make default vendor for product has no vendor
        $vendor = $this->_vendorFactory->create(); //Vnecoms\Vendors\Model\Vendor
        $vendor->setVendorId('default');
        $vendor->setEntityId(0);
        $allVendors[] = $vendor;
        foreach ($this->request->getAllItems() as $item) {
            $vendor_id = $item->getVendorId();
            if ($vendor_id && $vendor_id != 'default' && !isset($allVendorIds[$vendor_id])) {
                $allVendors[] = $this->_vendorFactory->create()->load($vendor_id); //Vnecoms\Vendors\Model\Vendor
                $allVendorIds[$vendor_id] = $vendor_id;
            }
        }
        // set vendor item weight
        foreach ($allVendors as &$vendor) {
            $this->getAddShipmentDetailsByVendor($vendor, $this->request->getAllItems());
        }
        $priceArr = [];
        $requestFromAramex = [];
        foreach ($allowed_methods as $m_value => $m_title) {
            $params['ShipmentDetails']['ProductType'] = $m_value;
            if ($m_value == "CDA") {
                $params['ShipmentDetails']['Services'] = "CODS";
            } else {
                $params['ShipmentDetails']['Services'] = "";
            }
            // request by vendor
            foreach($allVendors as &$vendor){
                // check for allowed vendor by country, but allow from admin
                if ($vendor->getVendorId() != 'default' && !$this->isAllowVendor($vendor)) {
                    continue;
                }
                $vendor_id = $vendor->getVendorId();
                if ($vendor_id != 'default') {
                    if ($vendor->getCountryId() && $vendor->getRegion() && $vendor->getCity() && $vendor->getPostcode()) {
                        $params['OriginAddress'] = [
                            'StateOrProvinceCode' => $vendor->getRegion(),
                            'City' => $vendor->getCity(),
                            'PostCode' => $vendor->getPostcode(),
                            'CountryCode' => $vendor->getCountryId(),
                        ];
                    } else {
                        continue;
                    }
                    $m_code = $m_value.'_'.$vendor->getVendorId().self::SEPARATOR.$vendor->getEntityId();
                } else {
                    $m_code = $m_value.'_'.$vendor->getVendorId().self::SEPARATOR.$vendor->getVendorId();
                }

                // set ShipmentDetails by vendor
                $params['ShipmentDetails'] = array_merge($params['ShipmentDetails'], [
                    // 'ProductGroup' => $product_group,
                    // 'ProductType' => '',
                    'ActualWeight' => ['Value' => $vendor->getWeightPounds(), 'Unit' => 'KG'],
                    'ChargeableWeight' => ['Value' => $vendor->getWeightPounds(), 'Unit' => 'KG'],
                    'NumberOfPieces' => $vendor->getPkgQty()
                ]);

                // check not allowed the sample country
                if ($params['OriginAddress']['CountryCode'] == $params['DestinationAddress']['CountryCode']) {
                    continue;
                }
                $requestFromAramex = $this->makeRequestToAramex($params, $m_code, $m_title);
                if (isset($requestFromAramex['response']['error'])) {
                    continue;
                }
                foreach ($requestFromAramex['priceArr'] as $method => &$values) {
                    $values['vendor_id'] = $vendor_id;
                }
                $priceArr[] = $requestFromAramex['priceArr'];
            }
        }

        $result = $this->sendResult($priceArr, $requestFromAramex);
        return $result;
    }
    
    /**
     * Makes request to Aramex server
     *
     * @param array $params Parameters
     * @param string $m_value Shipping method value
     * @param string $m_title Shipping method name
     * @return array Response from Aramex server
     */
    private function makeRequestToAramex($params, $m_value, $m_title)
    {
        $priceArr = [];
        $baseUrl = $this->helper->getWsdlPath();
        $soapClient = $this->soapClientFactory->create($baseUrl .
                    'aramex-rates-calculator-wsdl.wsdl', ['version' => SOAP_1_1,'trace' => 1, 'keep_alive' => false]);
        try {
            $results = $soapClient->CalculateRate($params);
            if ($results->HasErrors) {
                if (is_array($results->Notifications->Notification)) {
                    $error = "";
                    foreach ($results->Notifications->Notification as $notify_error) {
                        $error .= 'Aramex: ' . $notify_error->Code . ' - ' . $notify_error->Message . "  *******  ";
                    }
                    $response['error'] = $error;
                } else {
                    $response['error'] = 'Aramex: ' . $results->Notifications->Notification->Code . ' - ' .
                            $results->Notifications->Notification->Message;
                }
                $response['type'] = 'error';
            } else {
                $response['type'] = 'success';
                $priceArr[$m_value] = [
                        'label' => $m_title,
                        'amount' => $results->TotalAmount->Value,
                        'currency' => $results->TotalAmount->CurrencyCode
                        ];
            }
        } catch (\Exception $e) {
            $response['type'] = 'error';
            $response['error'] = $e->getMessage();
        }
        return[ 'priceArr' => $priceArr, 'response' => $response];
    }
    
    /**
     * Saves result to Magento
     *
     * @param array $priceArr Price array
     * @param array $requestFromAramex Rates from Aramex server
     * @return object
     */
    private function sendResult($priceArr, $requestFromAramex)
    {
        $result = $this->_rateResultFactory->create();
        if (empty($priceArr[0])) {
            if (isset($requestFromAramex['response'])) {
                $error = $this->_rateErrorFactory->create();
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setErrorMessage($requestFromAramex['response']['error']);
                $result->append($error);
                return $error;
            }
        } else {
            foreach ($priceArr as $priceArr1) {
                foreach ($priceArr1 as $method => $values) {
                    $rate = $this->_rateMethodFactory->create();
                    $rate->setVendorId($values['vendor_id']);
                    $rate->setCarrier($this->_code);
                    $rate->setCarrierTitle($this->getConfigData('title'));
                    $rate->setMethod($method);
                    $rate->setMethodTitle($values['label']);
                    $rate->setPrice($values['amount']);
                    $rate->setCost($values['amount']);
                    $result->append($rate);
                }
            }
        }
        return $result;
    }
    
    /**
     * Proccesses Additional Validation
     *
     * @param object $request Request
     * @return bool Response
     */
    public function proccessAdditionalValidation(\Magento\Framework\DataObject $request)
    {
        return true;
    }
    
    /**
     * Gets tracking
     *
     * @param array|object $trackings Tracking data
     * @return object Result of tracking
     */
    public function getTracking($trackings)
    {
        $this->setTrackingReqeust();
        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }

        $this->_getXmlTracking($trackings);
        return $this->result;
    }
    
    /**
     * Get XML format of tracking
     *
     * @param array $trackings Tracking data
     * @return void
     */
    private function _getXmlTracking($trackings)
    {
        $r = $this->_rawTrackRequest;
        foreach ($trackings as $tracking) {
            $this->_parseXmlTrackingResponse($tracking);
        }
    }
    
    /**
     * Sets tracking reqeust
     *
     * @return void
     */
    private function setTrackingReqeust()
    {
        $r = $this->objectFactory->create();
        $userId = $this->getConfigData('userid');
        $r->setUserId($userId);
        $this->_rawTrackRequest = $r;
    }
    
    /**
     * Parses Xml Tracking Response
     *
     * @param string $trackingvalue Tracking value
     * @return void
     */
    private function _parseXmlTrackingResponse($trackingvalue)
    {
        $resultArr = [];
        if (!$this->result) {
            $this->result = $this->_trackFactory->create();
        }
        $defaults = $this->getDefaults();
        $url = $this->defaultGatewayUrl;

        //SOAP object
        $clientAramex = $this->soapClientFactory->create($url .
                    'Tracking.wsdl', ['version' => SOAP_1_1,'trace' => 1, 'keep_alive' => false]);

        $aramexParams = $this->_getAuthDetails();
        $aramexParams['Transaction'] = ['Reference1' => '001'];
        $aramexParams['Shipments'] = [$trackingvalue];
        $_resAramex = $clientAramex->TrackShipments($aramexParams);

        if (is_object($_resAramex) && !$_resAramex->HasErrors) {
            $tracking = $this->_trackStatusFactory->create();
            $tracking->setCarrier('aramex');
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setUrl('https://www.aramex.com/track/results?mode=0&ShipmentNumber=' . $trackingvalue);
            $tracking->setTracking($trackingvalue);
            $this->result->append($tracking);
        } else {
            $errorMessage = '';
            foreach ($_resAramex->Notifications as $notification) {
                $errorMessage .= '<b>' . $notification->Code . '</b>' . $notification->Message;
            }
            $error = $this->_trackErrorFactory->create();
            $error->setCarrier('aramex');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setTracking($trackingvalue);
            $error->setErrorMessage($errorMessage);
            $this->result->append($error);
        }
    }
    
    /**
     * Gets tracking infoTable
     *
     * @param object $HAWBHistory Tracking data
     * @return string Table with tracking data
     */
    public function getTrackingInfoTable($HAWBHistory)
    {
        $checkArray = is_array($HAWBHistory);
        $resultTable = '<table summary="Item Tracking"  class="data-table">';
        $resultTable .= '<col width="1">
                          <col width="1">
                          <col width="1">
                          <col width="1">
                          <thead>
                          <tr class="first last">
                          <th>Location</th>
                          <th>Action Date/Time</th>
                          <th class="a-right">Tracking Description</th>
                          <th class="a-center">Comments</th>
                          </tr>
                          </thead><tbody>';
        if ($checkArray) {
            foreach ($HAWBHistory as $HAWBUpdate) {
                $resultTable .= '<tr>
                    <td>' . $HAWBUpdate->UpdateLocation . '</td>
                    <td>' . $HAWBUpdate->UpdateDateTime . '</td>
                    <td>' . $HAWBUpdate->UpdateDescription . '</td>
                    <td>' . $HAWBUpdate->Comments . '</td>
                    </tr>';
            }
        } else {
            $resultTable .= '<tr>
                    <td>' . $HAWBHistory->UpdateLocation . '</td>
                    <td>' . $HAWBHistory->UpdateDateTime . '</td>
                    <td>' . $HAWBHistory->UpdateDescription . '</td>
                    <td>' . $HAWBHistory->Comments . '</td>
                    </tr>';
        }
        $resultTable .= '</tbody></table>';
        return  $resultTable;
    }
    
    /**
     * Get auth details
     *
     * @return array Auth details
     */
    private function _getAuthDetails()
    {
        return [
            'ClientInfo' => [
                'AccountCountryCode' => $this->_scopeConfig->
            getValue('aramex/settings/account_country_code', self::SCOPE_STORE),
                'AccountEntity' => $this->_scopeConfig->getValue('aramex/settings/account_entity', self::SCOPE_STORE),
                'AccountNumber' => $this->_scopeConfig->getValue('aramex/settings/account_number', self::SCOPE_STORE),
                'AccountPin' => $this->_scopeConfig->getValue('aramex/settings/account_pin', self::SCOPE_STORE),
                'UserName' => $this->_scopeConfig->getValue('aramex/settings/user_name', self::SCOPE_STORE),
                'Password' => $this->_scopeConfig->getValue('aramex/settings/password', self::SCOPE_STORE),
                'Version' => 'v1.0'
            ]
        ];
    }

    /**
     * Check allow vendor in method
     * return boolean
     */
    protected function isAllowVendor($vendor){
        if (!$this->_sallowfromspecific) {
            $this->_sallowfromspecific = $this->_scopeConfig->getValue('carriers/aramex/sallowfromspecific', self::SCOPE_STORE);
        }
        if (!$this->_fromspecificcountry) {
            $this->_fromspecificcountry = $this->_scopeConfig->getValue('carriers/aramex/fromspecificcountry', self::SCOPE_STORE);
        }
        if ($this->_sallowfromspecific) {
            $availableCountries = [];
            if ($this->_fromspecificcountry) {
                $availableCountries = explode(',', $this->_fromspecificcountry);
            }
            if (!empty($availableCountries) && $vendor && in_array($vendor->getCountry(), $availableCountries)) {
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * return $vendor
     */
    protected function getAddShipmentDetailsByVendor(&$vendor, $items){
        $totalWeight = 0;
        $pkgQty = 0;
        foreach($items as $item){
            if ($item->getVendorId() == $vendor->getEntityId()) {
                $weight = $item->getRowWeight();
                if ((float) $weight) {
                    $totalWeight += $weight;
                }
                $pkgQty++;
            }
        }
        $vendor->setWeightPounds($totalWeight);
        $vendor->setPkgQty($pkgQty);
        return $this;
    }
}