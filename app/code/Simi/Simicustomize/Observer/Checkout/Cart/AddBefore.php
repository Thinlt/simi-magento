<?php

namespace Simi\Simicustomize\Observer\Checkout\Cart;

use Magento\Framework\Event\ObserverInterface;


class AddBefore implements ObserverInterface {
    public $simiObjectManager;

    /**
     * \Magento\Checkout\Model\Session
     */
    protected $session;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Checkout\Model\Session $session
    ) {
        $this->simiObjectManager = $simiObjectManager;
        $this->session = $session;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $requestInfo = $observer->getEvent()->getData('info');
        $session = $this->session;
        $product = $observer->getEvent()->getData('product');
        if ($items = $this->_getCart()->getItems()) {
            $itemIds = [];
            foreach($items as $item){
                $itemIds[] = $item->getId();
            }
            // reset session
            if (!count($itemIds)) {
                $this->session->setData('try_to_buy', []);
                $this->session->setData('reservable', []);
                $this->session->setData('pre_order', []);
            }
            if (isset($requestInfo['product']) && !in_array($requestInfo['product'], $itemIds)) {
                $itemIds[] = $requestInfo['product'];
            }
            // processing cart with try to buy product
            $message = 'Cannot add this product type to cart. Remove another type in your cart and try again.';
            if (isset($requestInfo['try_to_buy']) && (int)$requestInfo['try_to_buy']) {
                if (!empty($session->getData('reservable')) || !empty($session->getData('pre_order'))) {
                    throw new \Exception(__($message));
                }
                if (!$product->getTryToBuy()) {
                    throw new \Exception(__('Try to buy for this product does not allowed'));
                }
                $this->session->setData('try_to_buy', $itemIds);
            }
            if (isset($requestInfo['reservable']) && (int)$requestInfo['reservable']) {
                if (!empty($session->getData('try_to_buy')) || !empty($session->getData('pre_order'))) {
                    throw new \Exception(__($message));
                }
                if (!$product->getReservable()) {
                    throw new \Exception(__('Reserve for this product does not allowed'));
                }
                $this->session->setData('reservable', $itemIds);
            }
            if (isset($requestInfo['pre_order']) && (int)$requestInfo['pre_order']) {
                if (!empty($session->getData('try_to_buy')) || !empty($session->getData('reservable'))) {
                    throw new \Exception(__($message));
                }
                if (!$product->getPreOrder()) {
                    throw new \Exception(__('Pre-order for this product does not allowed'));
                }
                $this->session->setData('pre_order', $itemIds);
            }
        }
    }

    protected function _getCart()
    {
        return $this->simiObjectManager->get('Magento\Checkout\Model\Cart');
    }
}