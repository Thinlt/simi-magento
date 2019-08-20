<?php

namespace Vnecoms\PdfPro\Observer;

/**
 * Class AbstractSendCreditmemoObserver.
 */
class AbstractSendCreditmemoObserver extends AbstractObserver
{
    const XML_PATH_CREDITMEMO_ATTACH_PDF = 'pdfpro/general/creditmemo_email_attach';

    /**
     * @var \Vnecoms\PdfPro\Model\Order\Creditmemo
     */
    private $creditmemo;

    /**
     * AbstractSendCreditmemoObserver constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Vnecoms\PdfPro\Model\ContentAttacher $contentAttacher
     * @param \Vnecoms\PdfPro\Model\Order\Creditmemo $creditmemo
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer,
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\ContentAttacher $contentAttacher,
        \Vnecoms\PdfPro\Model\Order\Creditmemo $creditmemo
    ) {
        parent::__construct($scopeConfig, $pdfRenderer, $helper, $contentAttacher);
        $this->creditmemo = $creditmemo;
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
         * @var \Magento\Sales\Api\Data\CreditmemoInterface
         */
        $creditmemo = $observer->getCreditmemo();
        $config = $this->helper->getConfig(static::XML_PATH_CREDITMEMO_ATTACH_PDF);

        if ($config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_NO) {
            return;
        }

        $creditmemoData = $this->creditmemo->initCreditmemoData($creditmemo);

        $this->attachPdf(
            'creditmemo',
            $this->pdfRenderer->getPdfContent('creditmemo', array($creditmemoData)),
            $this->pdfRenderer->getFileName('creditmemo', $creditmemo),
            $observer->getAttachmentContainer()
        );
    }
}
