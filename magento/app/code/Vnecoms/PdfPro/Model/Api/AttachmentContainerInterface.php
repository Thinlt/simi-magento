<?php

namespace Vnecoms\PdfPro\Model\Api;

/**
 * Interface AttachmentContainerInterface.
 */
interface AttachmentContainerInterface
{
    /**
     * @return bool
     */
    public function hasAttachments();

    /**
     * @param AttachmentInterface $attachment
     */
    public function addAttachment(AttachmentInterface $attachment);

    public function getAttachments();

    public function resetAttachments();
}
