<?php

namespace Vnecoms\PdfPro\Observer;

/**
 * Class AbstractSendShipmentObserver.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class AbstractSendShipmentObserver extends AbstractObserver
{
    const XML_PATH_SHIPMENT_ATTACH_PDF = 'pdfpro/general/shipment_email_attach';

    /**
     * @var \Vnecoms\PdfPro\Model\Order\Shipment
     */
    private $shipment;

    /**
     * AbstractSendShipmentObserver constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Vnecoms\PdfPro\Model\ContentAttacher $contentAttacher
     * @param \Vnecoms\PdfPro\Model\Order\Shipment $shipment
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer,
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\ContentAttacher $contentAttacher,
        \Vnecoms\PdfPro\Model\Order\Shipment $shipment
    ) {
        parent::__construct($scopeConfig, $pdfRenderer, $helper, $contentAttacher);
        $this->shipment = $shipment;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $enable = $this->helper->getConfig('pdfpro/general/enabled');

        if ($enable == 0) {
            return;
        }

        /*
         * @var \Magento\Sales\Api\Data\ShipmentInterface
         */
        $shipment = $observer->getShipment();
        $config = $this->helper->getConfig(static::XML_PATH_SHIPMENT_ATTACH_PDF);

        if ($config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_NO) {
            return;
        }

        $shipmentData = $this->shipment->initShipmentData($shipment);

        $this->attachPdf(
            'shipment',
            $this->pdfRenderer->getPdfContent('shipment', array($shipmentData)),
            $this->pdfRenderer->getFileName('shipment', $shipment),
            $observer->getAttachmentContainer()
        );
    }
}
