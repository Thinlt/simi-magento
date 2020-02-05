<?php

namespace Vnecoms\PdfPro\Observer;

/**
 * Class AbstractSendInvoiceObserver.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class AbstractSendInvoiceObserver extends AbstractObserver
{
    const XML_PATH_INVOICE_ATTACH_PDF = 'pdfpro/general/invoice_email_attach';

    /**
     * @var \Vnecoms\PdfPro\Model\Order\Invoice
     */
    private $invoice;

    /**
     * AbstractSendInvoiceObserver constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Vnecoms\PdfPro\Model\ContentAttacher $contentAttacher
     * @param \Vnecoms\PdfPro\Model\Order\Invoice $invoice
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer,
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\ContentAttacher $contentAttacher,
        \Vnecoms\PdfPro\Model\Order\Invoice $invoice
    ) {
        parent::__construct($scopeConfig, $pdfRenderer, $helper, $contentAttacher);
        $this->invoice = $invoice;
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
         * @var \Magento\Sales\Api\Data\InvoiceInterface
         */
        $invoice = $observer->getInvoice();

        $config = $this->helper->getConfig(static::XML_PATH_INVOICE_ATTACH_PDF);

        if ($config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_NO) {
            return;
        }
        $invoiceData = $this->invoice->initInvoiceData($invoice);

        $this->attachPdf(
            'invoice',
            $this->pdfRenderer->getPdfContent('invoice', array($invoiceData)),
            $this->pdfRenderer->getFileName('invoice', $invoice),
            $observer->getAttachmentContainer()
        );
    }
}
