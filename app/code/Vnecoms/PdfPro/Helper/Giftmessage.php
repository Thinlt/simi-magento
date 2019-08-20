<?php

namespace Vnecoms\PdfPro\Helper;

/**
 * Class Giftmessage.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Giftmessage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $_messageHelper;
    /**
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\GiftMessage\Helper\Message $message
    ) {
        $this->_messageHelper = $message;
        parent::__construct($context);
    }

    /**
     * Initialize gift message for entity.
     *
     * @return \Magento\GiftMessage\Model\Message
     */
    public function initMessage($entity)
    {
        $order = ($entity instanceof \Magento\Sales\Model\Order) ? $entity : $entity->getOrder();

        if (!$order->getGiftMessageId()) {
            return false;
        }

        $giftMessage = $this->_messageHelper->getGiftMessage($order->getGiftMessageId());
        // init default values for giftmessage form
        if (!$giftMessage->getSender()) {
            $giftMessage->setSender($order->getCustomerName());
        }
        if (!$giftMessage->getRecipient()) {
            if ($order->getShippingAddress()) {
                $defaultRecipient = $order->getShippingAddress()->getName();
            } elseif ($order->getBillingAddress()) {
                $defaultRecipient = $order->getBillingAddress()->getName();
            }
            $giftMessage->setRecipient($defaultRecipient);
        }

        return new \Magento\Framework\DataObject($giftMessage->getData());
    }
}
