<?php
/**
 * Copyright © Vnecoms. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Model\Order\Email;

use Vnecoms\VendorsSales\Model\Order as VendorsOrder;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Address\Renderer;
use Vnecoms\Vendors\Model\VendorFactory;

abstract class Sender
{
    /**
     * @var \Magento\Sales\Model\Order\Email\SenderBuilderFactory
     */
    protected $senderBuilderFactory;

    /**
     * @var Template
     */
    protected $templateContainer;

    /**
     * @var IdentityInterface
     */
    protected $identityContainer;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * @var \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * @param Template $templateContainer
     * @param IdentityInterface $identityContainer
     * @param \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param VendorFactory $vendorFactory
     * @param Renderer $addressRenderer
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        VendorFactory $vendorFactory,
        Renderer $addressRenderer
    ) {
        $this->templateContainer = $templateContainer;
        $this->identityContainer = $identityContainer;
        $this->senderBuilderFactory = $senderBuilderFactory;
        $this->logger = $logger;
        $this->vendorFactory = $vendorFactory;
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * @param VendorsOrder $vendorOrder
     * @return bool
     */
    protected function checkAndSend(VendorsOrder $vendorOrder)
    {
        $order = $vendorOrder->getOrder();
        $this->identityContainer->setVendorId($vendorOrder->getVendorId());
        $this->identityContainer->setStore($order->getStore());
        if (!$this->identityContainer->isEnabled()) {
            return false;
        }
        $this->prepareTemplate($vendorOrder);

        /** @var SenderBuilder $sender */
        $sender = $this->getSender();

        try {
            $sender->send();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
        try {
            $sender->sendCopyTo();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return true;
    }

    /**
     * Populate order email template with customer information.
     *
     * @param VendorsOrder $order
     * @return void
     */
    protected function prepareTemplate(VendorsOrder $order)
    {
        $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

        $templateId = $this->identityContainer->getTemplateId();

        $vendor = $this->vendorFactory->create()->load($order->getVendorId());
        $customerName = $vendor->getName();


        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($vendor->getEmail());
        $this->templateContainer->setTemplateId($templateId);
    }

    /**
     * Create Sender object using appropriate template and identity.
     *
     * @return Sender
     */
    protected function getSender()
    {
        return $this->senderBuilderFactory->create(
            [
                'templateContainer' => $this->templateContainer,
                'identityContainer' => $this->identityContainer,
            ]
        );
    }

    /**
     * Get template options.
     *
     * @return array
     */
    protected function getTemplateOptions()
    {
        return [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->identityContainer->getStore()->getStoreId()
        ];
    }

    /**
     * @param VendorsOrder $order
     * @return string|null
     */
    protected function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * @param VendorsOrder $order
     * @return string|null
     */
    protected function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }
}
