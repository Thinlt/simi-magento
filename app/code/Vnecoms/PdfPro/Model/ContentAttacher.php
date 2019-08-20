<?php

namespace Vnecoms\PdfPro\Model;

use Vnecoms\PdfPro\Model\Api\AttachmentContainerInterface as AttachmentContainerInterface;

class ContentAttacher
{
    const MIME_PDF = 'application/pdf';
    const TYPE_OCTETSTREAM = 'application/octet-stream';
    const MIME_TXT = 'text/plain';
    const MIME_HTML = 'text/html; charset=UTF-8';

    /**
     * @var AttachmentFactory
     */
    private $attachmentFactory;

    public function __construct(
        AttachmentFactory $attachmentFactory
    ) {
        $this->attachmentFactory = $attachmentFactory;
    }

    public function addGeneric(
        $config,
        $content,
        $filename,
        $mimeType,
        AttachmentContainerInterface $attachmentContainer
    ) {
        $attachment = $this->attachmentFactory->create(
            [
                'config' => $config,
                'content' => $content,
                'mimeType' => $mimeType,
                'fileName' => $filename
            ]
        );
        $attachmentContainer->addAttachment($attachment);
    }

    public function addPdf($config, $content, $pdfFilename, AttachmentContainerInterface $attachmentContainer)
    {
        $this->addGeneric($config, $content, $pdfFilename, self::MIME_PDF, $attachmentContainer);
    }

    public function addText($config, $text, $pdfFilename, AttachmentContainerInterface $attachmentContainer)
    {
        $this->addGeneric($config, $text, $pdfFilename, self::MIME_TXT, $attachmentContainer);
    }

    public function addHtml($config, $html, $pdfFilename, AttachmentContainerInterface $attachmentContainer)
    {
        $this->addGeneric($config, $html, $pdfFilename, self::MIME_HTML, $attachmentContainer);
    }
}
