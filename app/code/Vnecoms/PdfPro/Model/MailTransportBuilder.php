<?php

namespace Vnecoms\PdfPro\Model;

/**
 * Class MailTransportBuilder.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class MailTransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @param \Magento\Framework\DataObject $attachment
     */
    public function addAttachment(\Magento\Framework\DataObject $attachment)
    {
        $this->message->createAttachment(
            $attachment->getData('content'),
            'application/pdf',
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            $this->encodedFileName($attachment->getData('fileName'))
        );
    }

    public function addTypeAttachment(\Magento\Framework\DataObject $attachment)
    {
        return $attachment->getData('config');
    }

    protected function encodedFileName($subject)
    {
        return sprintf('=?utf-8?B?%s?=', base64_encode($subject));
    }
	
	public function getMessage(){
        return $this->message;
    }

	/**
     * Creates a \Zend_Mime_Part attachment
     * @param  string $attachment
     * @param  string $mimeType
     * @param  string $disposition
     * @param  string $encoding
     * @param  string $filename OPTIONAL A filename for the attachment
     * @return $this
     */
    public function createAttachment($attachment, $type, $disposition, $encoding, $name){
        $this->message->createAttachment($attachment, $type, $disposition, $encoding, $name);
        return $this;
    }
}
