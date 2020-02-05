<?php

namespace Vnecoms\PdfPro\Model\Api;

/**
 * Interface AttachmentInterface.
 */
interface AttachmentInterface
{
    const ENCODING_BASE64          = 'base64';
    const DISPOSITION_ATTACHMENT   = 'attachment';

    public function getMimeType();

    public function getFilename();

    public function getDisposition();

    public function getEncoding();

    public function getContent();
}
