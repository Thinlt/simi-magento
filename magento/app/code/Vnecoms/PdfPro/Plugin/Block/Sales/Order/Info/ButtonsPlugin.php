<?php
/**
 * Copyright © 2018 Vnecoms. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Vnecoms\PdfPro\Plugin\Block\Sales\Order\Info;

use Magento\Customer\Model\Context;

/**
 * Class with class map capability
 *
 * ...
 */
class ButtonsPlugin
{
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * ButtonsPlugin constructor.
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->helper = $helper;
        $this->httpContext = $httpContext;
    }

    /**
     * Get url for printing order.
     *
     * @param \Magento\Sales\Block\Order\Info\Buttons $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Model\Order $order
     *
     * @return string
     */
    public function aroundGetPrintUrl(
        \Magento\Sales\Block\Order\Info\Buttons $subject,
        \Closure $proceed,
        $order
    ) {
        if (!$this->helper->getConfig('pdfpro/general/enabled') || !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $proceed($order);
        }
        if (!$this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $subject->getUrl('pdfpro/guest/print', ['order_id' => $order->getId()]);
        }
        return $subject->getUrl('pdfpro/order/print', ['order_id' => $order->getId()]);
    }
}
