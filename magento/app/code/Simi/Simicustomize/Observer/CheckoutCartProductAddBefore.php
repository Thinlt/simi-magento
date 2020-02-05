<?php

namespace Simi\Simicustomize\Observer;

use Magento\Framework\Event\ObserverInterface;


class CheckoutCartProductAddBefore implements ObserverInterface {
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
//        $requestInfo = $observer->getEvent()->getData('info');
//        $session = $this->session;
//        $product = $observer->getEvent()->getData('product');
//        $this->_getQuote()->setIsPreorder(true);
//
//        //var_dump($product->getData('sku'));die;
    }

    protected function _getCart()
    {
        return $this->simiObjectManager->get('Magento\Checkout\Model\Cart');
    }

    public function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
}