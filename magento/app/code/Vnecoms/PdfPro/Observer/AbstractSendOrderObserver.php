<?php

namespace Vnecoms\PdfPro\Observer;

/**
 * Class AbstractSendOrderObserver.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class AbstractSendOrderObserver extends AbstractObserver
{
    const XML_PATH_ORDER_ATTACH_PDF = 'pdfpro/general/order_email_attach';

    /**
     * @var \Vnecoms\PdfPro\Model\Order
     */
    private $order;

    /**
     * AbstractSendOrderObserver constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Vnecoms\PdfPro\Model\ContentAttacher $contentAttacher
     * @param \Vnecoms\PdfPro\Model\Order $order
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer,
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\ContentAttacher $contentAttacher,
        \Vnecoms\PdfPro\Model\Order $order
    ) {
        parent::__construct($scopeConfig, $pdfRenderer, $helper, $contentAttacher);
        $this->order = $order;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $enable = $this->helper->getConfig('pdfpro/general/enabled');

        if ($enable == 0) {
            return;
        }

        /*
         * @var \Magento\Sales\Api\Data\OrderInterface
         */
        $order = $observer->getOrder();
        $config = $this->helper->getConfig(static::XML_PATH_ORDER_ATTACH_PDF);

        if ($config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_NO) {
            return;
        }

        $orderData = $this->order->initOrderData($order);

        $this->attachPdf(
            'order',
            $this->pdfRenderer->getPdfContent('order', array($orderData)),
            $this->pdfRenderer->getFileName('order', $order),
            $observer->getAttachmentContainer()
        );
    }
}
