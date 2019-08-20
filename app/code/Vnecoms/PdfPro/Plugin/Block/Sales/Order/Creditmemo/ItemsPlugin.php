<?php
/**
 * Copyright © 2018 Vnecoms. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Vnecoms\PdfPro\Plugin\Block\Sales\Order\Creditmemo;

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
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Sales\Block\Order\Creditmemo\Items $subject
     * @param \Closure $proceed
     * @param $creditmemo
     * @return string
     */
    public function aroundGetPrintCreditmemoUrl(
        \Magento\Sales\Block\Order\Creditmemo\Items $subject,
        \Closure $proceed,
        $creditmemo
    ) {
        if (!$this->helper->getConfig('pdfpro/general/enabled') || !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $proceed($creditmemo);
        }
        return $subject->getUrl('pdfpro/order/printCreditmemo', ['creditmemo_id' => $creditmemo->getId()]);
    }

    /**
     * @param \Magento\Sales\Block\Order\Creditmemo\Items $subject
     * @param \Closure $proceed
     * @param $order
     * @return mixed
     */
    public function aroundGetPrintAllCreditmemosUrl(
        \Magento\Sales\Block\Order\Creditmemo\Items $subject,
        \Closure $proceed,
        $order
    ) {
        if (!$this->helper->getConfig('pdfpro/general/enabled') || !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $proceed($order);
        }
        return $subject->getUrl('pdfpro/order/printCreditmemo', ['order_id' => $order->getId()]);
    }
}
