<?php
/**
 * PdfPro module observer
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\PdfPro\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;

class SetCookieDataObserver implements ObserverInterface
{
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * Cookie key for guest view
     */
    const COOKIE_NAME = 'guest-view';

    /**
     * Cookie path
     */
    const COOKIE_PATH = '/';

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;


    /**
     * SetCookieDataObserver constructor.
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param CookieManagerInterface $cookieManager
     * @param \Magento\Sales\Api\OrderRepositoryInterface|null $orderRepository
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        CookieMetadataFactory $cookieMetadataFactory,
        CookieManagerInterface $cookieManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository = null
    ) {
        $this->_helper = $helper;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieManager  = $cookieManager;
        $this->orderRepository = $orderRepository ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Sales\Api\OrderRepositoryInterface::class);

    }

    /**
     * Set cookie data
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\GoogleAdwords\Observer\SetConversionValueObserver
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->isEnableModule()) {
            return $this;
        }
        $orderIds = $observer->getEvent()->getOrderIds();
        $orderId   = $orderIds[0];
        if (!$orderIds || !is_array($orderIds)) {
            return $this;
        }

        //Loading order details
        $order  = $this->orderRepository->get($orderId);
        if (!$order || !$order instanceof \Magento\Sales\Model\Order) return $this;

        if (!$this->getGuestFromCookie()) {
            if ($order && $order instanceof \Magento\Sales\Model\Order) {
                $protectedCode = $order->getProtectCode();
                $incrementId = $order->getIncrementId();
            }
            $cookieData = $protectedCode . ':' . $incrementId;
            $cookieDataHash = base64_encode($cookieData);
            $this->setGuestCookie($cookieDataHash);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGuestFromCookie()
    {
        return $this->cookieManager->getCookie(self::COOKIE_NAME);
    }

    /**
     * Set guest-view cookie
     *
     * @param string $cookieData
     * @return void
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    private function setGuestCookie($cookieData)
    {
        $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setHttpOnly(true)
            ->setPath(self::COOKIE_PATH);

        $this->cookieManager->setPublicCookie(self::COOKIE_NAME, $cookieData, $cookieMetadata);
    }
}
