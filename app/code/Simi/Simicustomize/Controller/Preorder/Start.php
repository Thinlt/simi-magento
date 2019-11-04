<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Controller\Preorder;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\App\ObjectManager;

class Start extends \Magento\Framework\App\Action\Action
{
    /**
     * Payment map payment_code with url path
     */
    public $paymentMap = [
        'paypal_express' => 'paypal/express/start',
        'paypal_express_bml' => 'paypal/bml/start',
    ];
    
    /**
     * @var Magento\Quote\Api\Data\PaymentInterface
     */
    protected $paymentMethod;
    /**
     * @var Magento\Quote\Api\Data\AddressInterface
     */
    protected $billingAddress;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Magento\Sales\Model\Order $order,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress
    ){
        parent::__construct($context);
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->paymentMethod = $paymentMethod;
        $this->billingAddress = $billingAddress;
        $this->_checkoutSession = $checkoutSession;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->order = $order;
    }

    /**
     * Params $orderId increment_id
     * @return void
     */
    public function execute()
    {
        try {
            // $requestBody = '{"cartId":"z1bKw63L6lHT2jmMVfcX5TGU53ftYKs2","email":"fsdfag@dsaga.com","paymentMethod":{"method":"paypal_express","po_number":null,"additional_data":null},"billingAddress":{"countryId":"US","regionId":"2","regionCode":"AK","region":"Alaska","street":["23425364ynbfdbdfnsf"],"company":"","telephone":"1234325365","postcode":"99553","city":"Akutan","firstname":"dsgsad","lastname":"gsadg","saveInAddressBook":null}}';
            // $requestBody = json_decode($requestBody, true);
            $orderId = $this->getRequest()->getParam('orderId');
            if ($orderId) {
                $order = $this->order->loadByIncrementId($orderId);
                $payment = $order->getPayment();
                $paymentMethod = $this->paymentMethod
                    ->setMethod($payment->getMethod())
                    ->setPoNumber('')
                    ->setAdditionalData('');
    
                /**
                 * @var \Magento\Sales\Api\Data\OrderAddressInterface
                 */
                $orderBillingAddress = $order->getBillingAddress();
                $orderShippingAddress = $order->getShippingAddress();
    
                $shippingAddress = clone $this->billingAddress;
                $billingAddress = $this->billingAddress
                    ->addData($orderBillingAddress->getData())
                    ->setEmail($order->getCustomerEmail());
                $shippingAddress->addData($orderShippingAddress->getData());
    
                $this->_checkoutSession
                    ->clearQuote()
                    ->clearStorage()
                    ->setLoadInactive(false);

                //set order for quote change ReservedOrderId
                $this->_checkoutSession
                    ->setLastDepositOrderId($orderId);
    
                $cart = ObjectManager::getInstance()->create(\Magento\Checkout\Model\Cart::class); //create checkout cart
                $quote = $this->_checkoutSession->getQuote();
                $cart->setQuote($quote);
                // add order items to cart
                $allOrderItems = $order->getAllItems();
                foreach($allOrderItems as $item){
                    // $info = $item->getProductOptionByCode('info_buyRequest');
                    $options = $item->getProductOptions();
                    if (isset($options['info_buyRequest'])) {
                        if (isset($options['info_buyRequest']['pre_order'])) {
                            $options['info_buyRequest']['pre_order'] = 0;
                            $item->setProductOptions($options);
                        }
                    }
                    $cart->addOrderItem($item);
                }
    
                // disable pre-order at step 2
                foreach($quote->getAllVisibleItems() as $item){
                    $buyRequest = $item->getOptionByCode('info_buyRequest');
                    $data = $buyRequest->getValue() ? 
                        $this->serializer->unserialize($buyRequest->getValue()) : [];
                    if (isset($data['pre_order'])) {
                        $data['pre_order'] = 0;
                        $data = $this->serializer->serialize($data);
                        $buyRequest->setData('value', $data);
                    }
                }
    
                $shippingAddress->setShippingMethod('freeshipping_freeshipping')->setCollectShippingRates(true);
                $cartExtension = $quote->getExtensionAttributes();
                if ($cartExtension && $cartExtension->getShippingAssignments()) {
                    $cartExtension->getShippingAssignments()[0]
                        ->getShipping()
                        ->setMethod('freeshipping_freeshipping');
                }
                $quote
                    ->setReservedOrderId($orderId)
                    ->setShippingAddress($shippingAddress)
                    ->setBillingAddress($billingAddress)
                    ->collectTotals();
    
                $quote->save();
                $this->paymentMethodManagement->set($quote->getId(), $paymentMethod);
                $cart->save();
    
                $url = $this->_url->getUrl('checkout/index');
                if (isset($this->paymentMap[$payment->getMethod()])) {
                    $url = $this->_url->getUrl($this->paymentMap[$payment->getMethod()]);
                }
                if ($url) {
                    $this->getResponse()->setRedirect($url);
                    return;
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t start Checkout.')
            );
        }
        $this->_redirect('checkout/cart');
    }
}
