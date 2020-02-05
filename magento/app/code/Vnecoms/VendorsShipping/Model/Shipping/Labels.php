<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsShipping\Model\Shipping;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Address;
use Magento\Shipping\Model\Shipment\Request;
use Magento\Store\Model\ScopeInterface;
use Magento\User\Model\User;
use Vnecoms\VendorsSales\Model\Order;

/**
 * Shipping labels model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Labels extends \Magento\Shipping\Model\Shipping
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Shipping\Model\Shipment\Request
     */
    protected $_request;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Shipping\Model\Shipment\RequestFactory $shipmentRequestFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Framework\Math\Division $mathDivision
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Shipping\Model\Shipment\Request $request
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Shipping\Model\Shipment\RequestFactory $shipmentRequestFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\Math\Division $mathDivision,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Backend\Model\Auth\Session $authSession,
        Request $request
    ) {
        $this->_authSession = $authSession;
        $this->_request = $request;
        parent::__construct(
            $scopeConfig,
            $shippingConfig,
            $storeManager,
            $carrierFactory,
            $rateResultFactory,
            $shipmentRequestFactory,
            $regionFactory,
            $mathDivision,
            $stockRegistry
        );
    }

    /**
     * Prepare and do request to shipment
     *
     * @param Shipment $orderShipment
     * @param Order $vendorOrder
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function requestToShipment(Shipment $orderShipment,Order $vendorOrder)
    {
        $admin = $this->_authSession->getUser();
        $order = $orderShipment->getOrder();

        $vendor = $vendorOrder->getVendor();
        $shippingMethod = $vendorOrder->getShippingMethod(true);

        $shipmentStoreId = $orderShipment->getStoreId();
        $shipmentCarrier = $this->_carrierFactory->create($vendorOrder->getShippingMethod(true)->getCarrierCode());
        $baseCurrencyCode = $this->_storeManager->getStore($shipmentStoreId)->getBaseCurrencyCode();
        if (!$shipmentCarrier) {
            throw new LocalizedException(__('Invalid carrier: %1', $shippingMethod->getCarrierCode()));
        }

        $shipperRegionCode = $vendor->getRegionCode();
        if (!$shipperRegionCode) {
            $shipperRegionCode = $vendor->getRegion();
        }

        if (!$vendor->getFirstname()
            || !$vendor->getLastname()
            || !$vendor->getTelephone()
            || !$vendor->getStreet()
            || !$shipperRegionCode
            || !$vendor->getCity()
            || !$vendor->getPostcode()
            || !$vendor->getCountryId()) {
            throw new LocalizedException(
                __(
                    'We don\'t have enough information to create shipping labels. Please make sure your store information and settings are complete.'
                )
            );
        }

        /** @var $request \Magento\Shipping\Model\Shipment\Request */
        $request = $this->_shipmentRequestFactory->create();
        $request->setOrderShipment($orderShipment);
        $address = $order->getShippingAddress();

        $this->setShipperDetails($request, $vendor);

        $this->setRecipientDetails($request, $address);
        $this->setShippingOrig($request,$vendor);


        $request->setShippingMethod($shippingMethod->getMethod());
        $request->setPackageWeight($order->getWeight());
        $request->setPackages($orderShipment->getPackages());
        $request->setBaseCurrencyCode($baseCurrencyCode);
        $request->setStoreId($shipmentStoreId);
        $request->setVendorId($vendor->getId());


        return $shipmentCarrier->requestToShipment($request);
    }

    /**
     * Set shipment address orig into request
     * @param \Magento\Shipping\Model\Shipment\Request $request
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     * @return void
     */
    protected function setShippingOrig(
        Request $request,
        \Vnecoms\Vendors\Model\Vendor $vendor
    ) {

        $shipperRegionCode = $vendor->getRegionCode();
        if (!$shipperRegionCode) {
            $shipperRegionCode = $vendor->getRegion();
        }
        $request->setOrigPersonName($vendor->getName());
        $company = $vendor->getCompany() ? $vendor->getCompany() : $vendor->getVendorId();
        $request->setOrigCompanyName($company);
        $request->setOrigPhoneNumber($vendor->getTelephone());
        $request->setOrigEmail($vendor->getEmail());
        $request->setOrigStreetLine($vendor->getStreet());
        $request->setOrigStreetLine1($vendor->getStreet());
        $request->setOrigStreetLine2($vendor->getStreet());
        $request->setOrigCity($vendor->getCity());
        $request->setOrigState($shipperRegionCode);
        $request->setOrigPostal($vendor->getPostcode());
        $request->setOrigCountry($vendor->getCountryModel()->getIso2Code());
        $request->setOrigCountryId($vendor->getCountryModel()->getId());
    }

    /**
     * Set shipper details into request
     * @param \Magento\Shipping\Model\Shipment\Request $request
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     * @return void
     */
    protected function setShipperDetails(
        Request $request,
        \Vnecoms\Vendors\Model\Vendor $vendor
    ) {
        $shipperRegionCode = $vendor->getRegionCode();
        if (!$shipperRegionCode) {
            $shipperRegionCode = $vendor->getRegion();
        }
        $request->setShipperContactPersonName($vendor->getName());
        $request->setShipperContactPersonFirstName($vendor->getFirstname());
        $request->setShipperContactPersonLastName($vendor->getLastname());
        $company = $vendor->getCompany() ? $vendor->getCompany() : $vendor->getVendorId();
        $request->setShipperContactCompanyName($company);
        $request->setShipperContactPhoneNumber($vendor->getTelephone());
        $request->setShipperEmail($vendor->getEmail());
        $request->setShipperAddressStreet($vendor->getStreet());
        $request->setShipperAddressStreet1($vendor->getStreet());
        $request->setShipperAddressStreet2($vendor->getStreet());
        $request->setShipperAddressCity($vendor->getCity());
        $request->setShipperAddressStateOrProvinceCode($shipperRegionCode);
        $request->setShipperAddressPostalCode($vendor->getPostcode());
        $request->setShipperAddressCountryCode($vendor->getCountryModel()->getData("iso2_code"));
    }

    /**
     * Set recipient details into request
     * @param \Magento\Shipping\Model\Shipment\Request $request
     * @param \Magento\Sales\Model\Order\Address $address
     * @return void
     */
    protected function setRecipientDetails(Request $request, Address $address)
    {
        $request->setRecipientContactPersonName(trim($address->getFirstname() . ' ' . $address->getLastname()));
        $request->setRecipientContactPersonFirstName($address->getFirstname());
        $request->setRecipientContactPersonLastName($address->getLastname());
        $request->setRecipientContactCompanyName($address->getCompany());
        $request->setRecipientContactPhoneNumber($address->getTelephone());
        $request->setRecipientEmail($address->getEmail());
        $request->setRecipientAddressStreet(trim($address->getStreetLine(1) . ' ' . $address->getStreetLine(2)));
        $request->setRecipientAddressStreet1($address->getStreetLine(1));
        $request->setRecipientAddressStreet2($address->getStreetLine(2));
        $request->setRecipientAddressCity($address->getCity());
        $request->setRecipientAddressStateOrProvinceCode($address->getRegionCode() ?: $address->getRegion());
        $request->setRecipientAddressRegionCode($address->getRegionCode());
        $request->setRecipientAddressPostalCode($address->getPostcode());
        $request->setRecipientAddressCountryCode($address->getCountryId());
    }
}
