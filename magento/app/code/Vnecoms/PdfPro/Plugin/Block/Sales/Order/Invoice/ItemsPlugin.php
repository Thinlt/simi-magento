<?php
/**
 * Copyright © 2018 Vnecoms. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Vnecoms\PdfPro\Plugin\Block\Sales\Order\Invoice;

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
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * ItemsPlugin constructor.
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Sales\Block\Order\Invoice\Items $subject
     * @param \Closure $proceed
     * @param $invoice
     * @return mixed|string
     */
    public function aroundGetPrintInvoiceUrl(
        \Magento\Sales\Block\Order\Invoice\Items $subject,
        \Closure $proceed,
        $invoice
    ) {
        if (!$this->helper->getConfig('pdfpro/general/enabled') || !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $proceed($invoice);
        }
        $result = $subject->getUrl('pdfpro/order/printInvoice', ['invoice_id' => $invoice->getId()]);
        return $result;
    }

    /**
     * @param \Magento\Sales\Block\Order\Invoice\Items $subject
     * @param \Closure $proceed
     * @param $order
     * @return mixed
     */
    public function aroundGetPrintAllInvoicesUrl(
        \Magento\Sales\Block\Order\Invoice\Items $subject,
        \Closure $proceed,
        $order
    ) {
        if (!$this->helper->getConfig('pdfpro/general/enabled') || !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $proceed($order);
        }
        $result = $subject->getUrl('pdfpro/order/printInvoice', ['order_id' => $order->getId()]);
        return $result;
    }
}
