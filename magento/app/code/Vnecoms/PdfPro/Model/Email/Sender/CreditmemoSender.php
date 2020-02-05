<?php

namespace Vnecoms\PdfPro\Model\Email\Sender;

/**
 * Class CreditmemoSender.
 */
class CreditmemoSender extends \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender
{
    /**
     * @var \Vnecoms\PdfPro\Model\AttachmentContainerInterface
     */
    protected $attachmentContainer;

    /**
     * CreditmemoSender constructor.
     *
     * @param \Magento\Sales\Model\Order\Email\Container\Template           $templateContainer
     * @param \Magento\Sales\Model\Order\Email\Container\CreditmemoIdentity $identityContainer
     * @param \Magento\Sales\Model\Order\Email\SenderBuilderFactory         $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface                                      $logger
     * @param \Magento\Sales\Model\Order\Address\Renderer                   $addressRenderer
     * @param \Magento\Payment\Helper\Data                                  $paymentHelper
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo           $creditmemoResource
     * @param \Magento\Framework\App\Config\ScopeConfigInterface            $globalConfig
     * @param \Magento\Framework\Event\ManagerInterface                     $eventManager
     * @param \Vnecoms\PdfPro\Model\AttachmentContainer                     $attachmentContainer
     */
    public function __construct(
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Magento\Sales\Model\Order\Email\Container\CreditmemoIdentity $identityContainer,
        \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo $creditmemoResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Vnecoms\PdfPro\Model\AttachmentContainer $attachmentContainer
    ) {
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $paymentHelper,
            $creditmemoResource,
            $globalConfig,
            $eventManager
        );
        $this->attachmentContainer = $attachmentContainer;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param bool                                  $forceSyncMode
     *
     * @return bool
     */
    public function send(\Magento\Sales\Model\Order\Creditmemo $creditmemo, $forceSyncMode = false)
    {
        $helper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Vnecoms\PdfPro\Helper\Data');

        if ($helper->getConfig('pdfpro/general/enabled')) {
            $this->eventManager->dispatch(
                'pdfpro_before_send_creditmemo',
                [

                    'attachment_container' => $this->attachmentContainer,
                    'creditmemo' => $creditmemo,
                ]
            );
        }

        return parent::send($creditmemo, $forceSyncMode);
    }
}
