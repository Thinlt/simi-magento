<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Controller\Guest;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use \Magento\Sales\Model\Order;

/**
 * Class PrintAction.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class PrintAction extends \Vnecoms\PdfPro\Controller\AbstractController\PrintAction
{

    /**
     * @var string
     */
    private $inputExceptionMessage = 'You entered incorrect data. Please try again.';

    /**
     * Guest Print Order Action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws
     */
    public function execute()
    {
        if (!$this->helper->getConfig('pdfpro/general/enabled') ||
            !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $orderId = (int)$this->getRequest()->getParam('order_id');
        /*
        * @var \Magento\Sales\Model\Order
        */
        $order = $this->orderRepository->get($orderId);
        $customerEmail = $order->getCustomerEmail();

        $fromCookie = $this->cookieManager->getCookie(self::COOKIE_NAME);

        if (!$fromCookie) {
            return $resultRedirect->setPath('sales/guest/form');
        }

        if (($customerEmail !== $this->loadOrderFromCookie($fromCookie)->getCustomerEmail()) &&
            ($order !== $this->loadOrderFromCookie($fromCookie))) {
            return $resultRedirect->setPath('sales/guest/form');
        }

        $this->coreRegistry->register('current_email', $customerEmail);

        if (!$order) {
            $this->messageManager->addErrorMessage('Order request not exist');
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        $orderData = $this->pdfProOrder->initOrderData($order);

        try {
            $result = $this->helper->initPdf(array($orderData), 'order');
            if ($result['success']) {
                return $this->_fileFactory->create(
                    $this->helper->getFileName('order', $order) . '.pdf',
                    $result['content'],
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            } else {
                throw new \Exception($result['msg']);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('sales/order/view/order_id/' . $orderId);

            return $resultRedirect;
        }

    }

    /**
     * Load order from cookie
     *
     * @param string $fromCookie
     * @return Order
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    private function loadOrderFromCookie($fromCookie)
    {
        $cookieData = explode(':', base64_decode($fromCookie));
        $protectCode = isset($cookieData[0]) ? $cookieData[0] : null;
        $incrementId = isset($cookieData[1]) ? $cookieData[1] : null;
        if (!empty($protectCode) && !empty($incrementId)) {
            $order = $this->getOrderRecord($incrementId);
            if (hash_equals((string)$order->getProtectCode(), $protectCode)) {
                $this->setGuestViewCookie($fromCookie);
                return $order;
            }
        }
        throw new InputException(__($this->inputExceptionMessage));
    }

    /**
     * Get order by increment_id and store_id
     *
     * @param string $incrementId
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws InputException
     */
    private function getOrderRecord($incrementId)
    {
        $records = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter('increment_id', $incrementId)
                ->create()
        );
        if ($records->getTotalCount() < 1) {
            throw new InputException(__($this->inputExceptionMessage));
        }
        $items = $records->getItems();
        return array_shift($items);
    }

    /**
     * Set guest-view cookie
     *
     * @param string $cookieValue
     * @return void
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    private function setGuestViewCookie($cookieValue)
    {
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setPath(self::COOKIE_PATH)
            ->setHttpOnly(true);
        $this->cookieManager->setPublicCookie(self::COOKIE_NAME, $cookieValue, $metadata);
    }

    /**
     * Retrieve current email associated
     * @return string|mixed
     */
    protected function getCurrentEmail()
    {
        return $this->coreRegistry->registry('current_email');
    }
}

