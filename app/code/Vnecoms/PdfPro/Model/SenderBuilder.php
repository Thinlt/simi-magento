<?php

namespace Vnecoms\PdfPro\Model;

class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    /**
     * @var Api\AttachmentContainerInterface
     */
    protected $attachmentContainer;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * SenderBuilder constructor.
     *
     * @param \Magento\Sales\Model\Order\Email\Container\Template          $templateContainer
     * @param \Magento\Sales\Model\Order\Email\Container\IdentityInterface $identityContainer
     * @param \Magento\Framework\Mail\Template\TransportBuilder            $transportBuilder
     * @param Api\AttachmentContainerInterface                             $attachmentContainer
     * @param \Vnecoms\PdfPro\Helper\Data                                  $helper
     */
    public function __construct(
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Magento\Sales\Model\Order\Email\Container\IdentityInterface $identityContainer,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Vnecoms\PdfPro\Model\Api\AttachmentContainerInterface $attachmentContainer,
        \Vnecoms\PdfPro\Helper\Data $helper
    ) {
        parent::__construct($templateContainer, $identityContainer, $transportBuilder);
        $this->attachmentContainer = $attachmentContainer;
        $this->helper = $helper;
    }

    /**
     * @return Api\AttachmentContainerInterface
     */
    public function getAttachmentContainer()
    {
        return $this->attachmentContainer;
    }

    /**
     * attach our attachments from the current sender to the message
     * send to customer.
     */
    public function send()
    {
        if ($this->attachmentContainer->hasAttachments()) {
            foreach ($this->attachmentContainer->getAttachments() as $attachment) {
                //check type of invoice and configuration for attachment
                $config = $this->helper->getConfig('pdfpro/general/'.$attachment->getData('config').'_email_attach');
                if ($config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_BOTH ||
                    $config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_CUSTOMER) {
                    $this->transportBuilder->addAttachment($attachment);
                }
            }
            $this->attachmentContainer->resetAttachments();
        }
        parent::send();
    }

    /**
     * attach our attachments from the current sender to the message
     * send to admin.
     */
    public function sendCopyTo()
    {
        if ($this->attachmentContainer->hasAttachments()) {
            foreach ($this->attachmentContainer->getAttachments() as $attachment) {
                $config = $this->helper->getConfig('pdfpro/general/'.$attachment->getData('config').'_email_attach');
                if ($config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_BOTH ||
                    $config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_ADMIN) {
                    $this->transportBuilder->addAttachment($attachment);
                }
            }
            $this->attachmentContainer->resetAttachments();
        }
        parent::sendCopyTo();
        $this->attachmentContainer->resetAttachments();
    }
}
