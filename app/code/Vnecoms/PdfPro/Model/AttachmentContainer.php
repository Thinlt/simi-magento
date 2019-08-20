<?php

namespace Vnecoms\PdfPro\Model;

class AttachmentContainer implements Api\AttachmentContainerInterface
{
    private $attachments = [];

    /**
     * @return bool
     */
    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    /**
     * @param Api\AttachmentInterface $attachment
     */
    public function addAttachment(Api\AttachmentInterface $attachment)
    {
        $this->attachments[] = $attachment;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @return void
     */
    public function resetAttachments()
    {
        $this->attachments = [];
    }
}
