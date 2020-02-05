<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsShipping\Model;

use Magento\Sales\Model\Order\Shipment;

class Info extends \Magento\Shipping\Model\Info
{


    /**
     * Generating tracking info
     *
     * @param array $hash
     * @return $this
     */
    public function loadByHash($hash)
    {

        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $object_manager->get('Vnecoms\VendorsSales\Helper\Data');

        $data = $helper->decodeTrackingHash($hash);

        if (!empty($data)) {
            $this->setData($data['key'], $data['id']);
            $this->setProtectCode($data['hash']);

            if ($this->getVendorOrderId() > 0) {
                $this->getTrackingInfoByVendorOrder();
            } elseif ($this->getOrderId() > 0) {
                $this->getTrackingInfoByOrder();
            } elseif ($this->getShipId() > 0) {
                $this->getTrackingInfoByShip();
            } else {
                $this->getTrackingInfoByTrackId();
            }
        }
        return $this;
    }


    /**
     * Instantiate vendor order model
     *
     * @return \Vnecoms\VendorsSales\Model\Order|bool
     */
    protected function _initVendorOrder()
    {

        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $object_manager->get('Vnecoms\VendorsSales\Model\Order');

        /** @var \Vnecoms\VendorsSales\Model\Order $vendorOrder */
        $vendorOrder = $model->load($this->getVendorOrderId());

        if (!$vendorOrder->getId() || $this->getProtectCode() != $vendorOrder->getOrder()->getProtectCode()) {
            return false;
        }

        return $vendorOrder;
    }

    /**
     * Retrieve all tracking by order id
     *
     * @return array
     */
    public function getTrackingInfoByVendorOrder()
    {
        $shipTrack = [];
        $order = $this->_initVendorOrder();
        if ($order) {
            $shipments = $order->getShipmentsCollection();
            foreach ($shipments as $shipment) {
                $increment_id = $shipment->getIncrementId();
                $tracks = $this->_getTracksCollection($shipment);

                $trackingInfos = [];
                foreach ($tracks as $track) {
                    $trackingInfos[] = $track->getNumberDetail();
                }
                $shipTrack[$increment_id] = $trackingInfos;
            }
        }
        $this->_trackingInfo = $shipTrack;
        return $this->_trackingInfo;
    }


}
