<?php
/**
 * Copyright © 2018 Vnecoms. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Vnecoms\PdfPro\Plugin\Block\Shipping;

/**
 * Class with class map capability
 *
 * ...
 */
class ItemsPlugin
{
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * Items constructor.
     *
     * @param \Vnecoms\PdfPro\Helper\Data                      $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param array                                            $data
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Shipping\Block\Items $subject
     * @param \Closure $proceed
     * @param $shipment
     * @return string
     */
    public function aroundGetPrintShipmentUrl(
        \Magento\Shipping\Block\Items $subject,
        \Closure $proceed,
        $shipment
    ) {
        if (!$this->helper->getConfig('pdfpro/general/enabled') || !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $proceed($shipment);
        }
        return $subject->getUrl('pdfpro/order/printShipment', ['shipment_id' => $shipment->getId()]);
    }

    /**
     * @param \Magento\Shipping\Block\Items $subject
     * @param \Closure $proceed
     * @param $order
     * @return string
     */
    public function aroundGetPrintAllShipmentsUrl(
        \Magento\Shipping\Block\Items $subject,
        \Closure $proceed,
        $order
    ) {
        if (!$this->helper->getConfig('pdfpro/general/enabled') || !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $proceed($order);
        }
        return $subject->getUrl('pdfpro/order/printShipment', ['order_id' => $order->getId()]);
    }
}
