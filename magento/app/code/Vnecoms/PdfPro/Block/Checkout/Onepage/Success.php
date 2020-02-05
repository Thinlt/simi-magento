<?php

namespace Vnecoms\PdfPro\Block\Checkout\Onepage;

use Magento\Sales\Model\Order;

/**
 * One page checkout success page.
 */
class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Checkout\Block\Onepage\Success'));

        return parent::_toHtml();
    }

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $_helper;

    /**
     * Success constructor.
     *
     * @param \Vnecoms\PdfPro\Helper\Data                      $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param Order\Config                                     $orderConfig
     * @param \Magento\Framework\App\Http\Context              $httpContext
     * @param array                                            $data
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
    }

    /**
     * Prepares block data.
     */
    protected function prepareBlockData()
    {
        if (!$this->_helper->getConfig('pdfpro/general/enabled')
            || !$this->_helper->getConfig('pdfpro/general/allow_customer_print')) {
            parent::prepareBlockData();

            return;
        }

        $order = $this->_checkoutSession->getLastRealOrder();

        $this->addData(
            [
                'is_order_visible' => $this->isVisible($order),
                'view_order_url' => $this->getUrl(
                    'sales/order/view/',
                    ['order_id' => $order->getEntityId()]
                ),
                'print_url' => $this->getUrl(
                    'pdfpro/order/print',
                    ['order_id' => $order->getEntityId()]
                ),
                'can_print_order' => $this->isVisible($order),
                'can_view_order' => $this->canViewOrder($order),
                'order_id' => $order->getIncrementId(),
            ]
        );
    }
}
