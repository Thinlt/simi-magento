<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Shipment view form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Vnecoms\VendorsShipping\Block\Vendors\Order\Shipment\View;

class Form extends \Magento\Shipping\Block\Adminhtml\View\Form
{
    /**
     * Get vendor order
     * @return /Vnecoms/VendorsSales/Model/Order
     */
    public function getVendorOrder()
    {
        return $this->_coreRegistry->registry('vendor_order');
    }

    /**
     * Get price data object
     *
     * @return Order|mixed
     */
    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if ($obj === null) {
            return $this->getVendorOrder();
        }
        return $obj;
    }
    /**
     * can view shipping tracking , create lable shipment online
     * @return bool
     */
    public function canViewShippingInfo()
    {
        if (is_object($this->getVendorOrder()->getShippingMethod())
            && !$this->getVendorOrder()->getShippingMethod()->getMethod()) {
            return false;
        } elseif (!$this->getVendorOrder()->getShippingMethod()) {
            return false;
        }
        return true;
    }
    /**
     * Check is carrier has functionality of creation shipping labels
     *
     * @return bool
     */
    public function canCreateShippingLabel()
    {
        $shippingCarrier = $this->_carrierFactory->create(
            $this->getVendorOrder()->getShippingMethod(true)->getCarrierCode()
        );
        return $shippingCarrier && $shippingCarrier->isShippingLabelsAvailable();
    }

    /**
     * Get create label button html
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getCreateLabelButton()
    {
        $data['shipment_id'] = $this->getShipment()->getId();
        $url = $this->getUrl('sales/order_shipment/createLabel', $data);
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'label' => __('Create Shipping Label...'),
                'onclick' => 'packaging.showWindow();',
                'class' => 'action-create-label'
            ]
        )->toHtml();
    }

    /**
     * Get print label button html
     *
     * @return string
     */
    public function getPrintLabelButton()
    {
        $data['shipment_id'] = $this->getShipment()->getId();
        $url = $this->getUrl('sales/order_shipment/printLabel', $data);
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Print Shipping Label'), 'onclick' => 'setLocation(\'' . $url . '\')']
        )->toHtml();
    }
}
