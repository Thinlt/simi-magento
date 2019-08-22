<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Block\Adminhtml\Shipment;

/**
 * Adminhtml shipment packaging
 */
class Packaging extends \Vnecoms\VendorsSales\Block\Adminhtml\Shipment\Packaging
{

    /**
     * Configuration for popup window for packaging
     *
     * @return string
     */
    public function getConfigDataJson()
    {
        $shipmentId = $this->getShipment()->getId();
        $orderId = $this->getRequest()->getParam('vorder_id');
        $urlParams = [];

        $itemsQty = [];
        $itemsPrice = [];
        $itemsName = [];
        $itemsWeight = [];
        $itemsProductId = [];
        $itemsOrderItemId = [];

        if ($shipmentId) {
            $urlParams['shipment_id'] = $shipmentId;
            $createLabelUrl = $this->getUrl('vendors/sales_shipment/createLabel', $urlParams);
            $itemsGridUrl = $this->getUrl('vendors/sales_shipment/getShippingItemsGrid', $urlParams);
            foreach ($this->getShipment()->getAllItems() as $item) {
                $itemsQty[$item->getId()] = $item->getQty();
                $itemsPrice[$item->getId()] = $item->getPrice();
                $itemsName[$item->getId()] = $item->getName();
                $itemsWeight[$item->getId()] = $item->getWeight();
                $itemsProductId[$item->getId()] = $item->getProductId();
                $itemsOrderItemId[$item->getId()] = $item->getOrderItemId();
            }
        } else {
            if ($orderId) {
                $urlParams['vorder_id'] = $orderId;
                $createLabelUrl = $this->getUrl('vendors/sales_shipment/save', $urlParams);
                $itemsGridUrl = $this->getUrl('vendors/sales_shipment/getShippingItemsGrid', $urlParams);

                foreach ($this->getShipment()->getAllItems() as $item) {
                    $itemsQty[$item->getOrderItemId()] = $item->getQty() * 1;
                    $itemsPrice[$item->getOrderItemId()] = $item->getPrice();
                    $itemsName[$item->getOrderItemId()] = $item->getName();
                    $itemsWeight[$item->getOrderItemId()] = $item->getWeight();
                    $itemsProductId[$item->getOrderItemId()] = $item->getProductId();
                    $itemsOrderItemId[$item->getOrderItemId()] = $item->getOrderItemId();
                }
            }
        }
        $data = [
            'createLabelUrl' => $createLabelUrl,
            'itemsGridUrl' => $itemsGridUrl,
            'errorQtyOverLimit' => __(
                'You are trying to add a quantity for some products that doesn\'t match the quantity that was shipped.'
            ),
            'titleDisabledSaveBtn' => __('Products should be added to package(s)'),
            'validationErrorMsg' => __('The value that you entered is not valid.'),
            'shipmentItemsQty' => $itemsQty,
            'shipmentItemsPrice' => $itemsPrice,
            'shipmentItemsName' => $itemsName,
            'shipmentItemsWeight' => $itemsWeight,
            'shipmentItemsProductId' => $itemsProductId,
            'shipmentItemsOrderItemId' => $itemsOrderItemId,
            'customizable' => $this->_getCustomizableContainers(),
        ];
        return $this->_jsonEncoder->encode($data);
    }

    /**
     * Return container types of carrier
     *
     * @return array
     */
    public function getContainers()
    {
        $order = $this->getShipment()->getOrder();
        $storeId = $this->getShipment()->getStoreId();
        $address = $order->getShippingAddress();
        $carrier = $this->_carrierFactory->create($this->getVendorOrder()->getShippingMethod(true)->getCarrierCode());

        $vendor = $this->getVendorOrder()->getVendor();

        $countryShipper = $vendor->getCountryId();

        if ($carrier) {
            $params = new \Magento\Framework\DataObject(
                [
                    'method' => $order->getShippingMethod(true)->getMethod(),
                    'country_shipper' => $countryShipper,
                    'country_recipient' => $address->getCountryId(),
                ]
            );
            return $carrier->getContainerTypes($params);
        }
        return [];
    }

    /**
     * Get codes of customizable container types of carrier
     *
     * @return array
     */
    protected function _getCustomizableContainers()
    {
        $order = $this->getVendorOrder();
        $carrier = $this->_carrierFactory->create($order->getShippingMethod(true)->getCarrierCode());
        if ($carrier) {
            return $carrier->getCustomizableContainerTypes();
        }
        return [];
    }

    /**
     * Return name of container type by its code
     *
     * @param string $code
     * @return string
     */
    public function getContainerTypeByCode($code)
    {
        $order = $this->getVendorOrder();
        $carrier = $this->_carrierFactory->create($order->getShippingMethod(true)->getCarrierCode());
        if ($carrier) {
            $containerTypes = $carrier->getContainerTypes();
            $containerType = !empty($containerTypes[$code]) ? $containerTypes[$code] : '';
            return $containerType;
        }
        return '';
    }

    /**
     * Return name of delivery confirmation type by its code
     *
     * @param string $code
     * @return string
     */
    public function getDeliveryConfirmationTypeByCode($code)
    {

        $countryId = $this->getShipment()->getOrder()->getShippingAddress()->getCountryId();
        $order = $this->getShipment()->getOrder();
        $carrier = $this->_carrierFactory->create($this->getVendorOrder()->getShippingMethod(true)->getCarrierCode());
        if ($carrier) {
            $params = new \Magento\Framework\DataObject(['country_recipient' => $countryId]);
            $confirmationTypes = $carrier->getDeliveryConfirmationTypes($params);
            $confirmationType = !empty($confirmationTypes[$code]) ? $confirmationTypes[$code] : '';
            return $confirmationType;
        }
        return '';
    }


    /**
     * Return delivery confirmation types of current carrier
     *
     * @return array
     */
    public function getDeliveryConfirmationTypes()
    {
        $countryId = $this->getShipment()->getOrder()->getShippingAddress()->getCountryId();
        $order = $this->getVendorOrder();
        $carrier = $this->_carrierFactory->create($order->getShippingMethod(true)->getCarrierCode());
        $params = new \Magento\Framework\DataObject(['country_recipient' => $countryId]);
        if ($carrier && is_array($carrier->getDeliveryConfirmationTypes($params))) {
            return $carrier->getDeliveryConfirmationTypes($params);
        }
        return [];
    }

    /**
     * Return content types of package
     *
     * @return array
     */
    public function getContentTypes()
    {
        $order = $this->getShipment()->getOrder();
        $storeId = $this->getShipment()->getStoreId();
        $address = $order->getShippingAddress();
        $carrier = $this->_carrierFactory->create($this->getVendorOrder()->getShippingMethod(true)->getCarrierCode());
        $vendor = $this->getVendorOrder()->getVendor();

        $countryShipper = $vendor->getCountryId();
        if ($carrier) {
            $params = new \Magento\Framework\DataObject(
                [
                    'method' => $order->getShippingMethod(true)->getMethod(),
                    'country_shipper' => $countryShipper,
                    'country_recipient' => $address->getCountryId(),
                ]
            );
            return $carrier->getContentTypes($params);
        }
        return [];
    }
}
