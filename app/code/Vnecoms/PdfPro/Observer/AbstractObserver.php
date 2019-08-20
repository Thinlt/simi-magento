<?php

namespace Vnecoms\PdfPro\Observer;

use Vnecoms\PdfPro\Model\Api\AttachmentContainerInterface as AttachmentContainerInterface;

/**
 * Class AbstractObserver.
 *
 * @author Vnecoms team <vnecoms.com>
 */
abstract class AbstractObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Vnecoms\PdfPro\Model\Api\PdfRendererInterface
     */
    protected $pdfRenderer;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * @var \Vnecoms\PdfPro\Model\ContentAttacher
     */
    protected $contentAttacher;

    /**
     * AbstractObserver constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Vnecoms\PdfPro\Model\ContentAttacher $contentAttacher
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer,
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\ContentAttacher $contentAttacher
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->pdfRenderer = $pdfRenderer;
        $this->helper = $helper;
        $this->contentAttacher = $contentAttacher;
    }

    /**
     * @param $config
     * @param $content
     * @param $pdfFilename
     * @param $mimeType
     * @param AttachmentContainerInterface $attachmentContainer
     */
    public function attachContent(
        $config,
        $content,
        $pdfFilename,
        $mimeType,
        AttachmentContainerInterface $attachmentContainer
    ) {
        $this->contentAttacher->addGeneric($config, $content, $pdfFilename, $mimeType, $attachmentContainer);
    }

    /**
     * @param $config
     * @param $pdfString
     * @param $pdfFilename
     * @param AttachmentContainerInterface $attachmentContainer
     */
    public function attachPdf($config, $pdfString, $pdfFilename, AttachmentContainerInterface $attachmentContainer)
    {
        $type = $config;
        $attached = $this->helper->getConfig('pdfpro/general/'.$type.'_email_attach');

        if ($attached != \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_NO) {
            $this->attachContent($config, $pdfString, $pdfFilename, 'application/pdf', $attachmentContainer);
        }
    }
}
