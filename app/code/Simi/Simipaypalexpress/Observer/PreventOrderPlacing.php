<?php

namespace Simi\Simipaypalexpress\Observer;

use Magento\Framework\Event\ObserverInterface;

class PreventOrderPlacing implements ObserverInterface
{

    private $simiObjectManager;
    public $new_added_product_sku = '';

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
   
        $this->simiObjectManager = $simiObjectManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderApiModel = $observer->getObject();
        if (($this->simiObjectManager->get('Magento\Checkout\Model\Type\Onepage')
                ->getQuote()->getPayment()->getMethod())
                && ($this->simiObjectManager->get('Magento\Checkout\Model\Type\Onepage')
                        ->getQuote()->getPayment()->getMethodInstance()->getCode() == 'paypal_express')) {
            $paymentRedirect = $this->simiObjectManager->get('Magento\Checkout\Model\Type\Onepage')
                    ->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if ($paymentRedirect && $paymentRedirect != '') {
                $orderApiModel->order_placed_info = array(
                    //'payment_redirect_url' => $paymentRedirect,
                    'payment_redirect' => 1,
                    'payment_method' => 'paypal_express'
                );
            }
            $orderApiModel->place_order = FALSE;
        }
    }

}
