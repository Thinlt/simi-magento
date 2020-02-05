<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends \Magento\Shipping\Helper\Data
{

    /**
     * Allowed hash keys
     *
     * @var array
     */
    protected $_allowedHashKeys = ['vendor_order_id','ship_id', 'order_id', 'track_id'];

    /**
     * Retrieve tracking url with params
     *
     * @param  string $key
     * @param  \Vnecoms\VendorsSales\Model\Order|\Magento\Sales\Model\Order|\Magento\Sales\Model\Order\Shipment|\Magento\Sales\Model\Order\Shipment\Track $model
     * @param  string $method Optional - method of a model to get id
     * @return string
     */
    protected function _getTrackingUrl($key, $model, $method = 'getId')
    {

        if ($model instanceof \Vnecoms\VendorsSales\Model\Order) {
            $protectCode = $model->getOrder()->getProtectCode();
        }else{
            $protectCode = $model->getProtectCode();
        }

        $urlPart = "{$key}:{$model->{$method}()}:{$protectCode}";

        $params = [
            '_direct' => 'shipping/tracking/popup',
            '_query' => ['hash' => $this->urlEncoder->encode($urlPart)]
        ];

        $storeModel = $this->_storeManager->getStore($model->getStoreId());
        return $storeModel->getUrl('', $params);
    }

    /**
     * Shipping tracking popup URL getter
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     * @return string
     */
    public function getTrackingPopupUrlBySalesModel($model)
    {
        if ($model instanceof \Vnecoms\VendorsSales\Model\Order) {
            return $this->_getTrackingUrl('vendor_order_id', $model);
        } elseif ($model instanceof \Magento\Sales\Model\Order) {
            return $this->_getTrackingUrl('order_id', $model);
        } elseif ($model instanceof \Magento\Sales\Model\Order\Shipment) {
            return $this->_getTrackingUrl('ship_id', $model);
        } elseif ($model instanceof \Magento\Sales\Model\Order\Shipment\Track) {
            return $this->_getTrackingUrl('track_id', $model, 'getEntityId');
        }
        return '';
    }

    /**
     * get split cart by vendor
     *
     * @return Ambigous <mixed, string, NULL, multitype:, multitype:Ambigous <string, multitype:, NULL> >
     */
    public function isSplitCartByVendor()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
        return $this->scopeConfig->getValue('vendors/sales/split_cart', $storeScope);
    }
}