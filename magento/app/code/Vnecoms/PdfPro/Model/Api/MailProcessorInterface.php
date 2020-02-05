<?php

namespace Vnecoms\PdfPro\Model\Api;

interface MailProcessorInterface
{
    public function createMultipartMessage(
        \Magento\Framework\Mail\MailMessageInterface $message,
        AttachmentContainerInterface $attachmentContainer
    );
}
