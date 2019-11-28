<?php
namespace Simi\Simicustomize\Model\Api;

class Startpreorderscompletes extends \Simi\Simiconnector\Model\Api\Apiabstract
{
    public function _getSession()
    {
        return $this->simiObjectManager->create('Magento\Checkout\Model\Session');
    }


    public function _getCart()
    {
        return $this->simiObjectManager->get('Magento\Checkout\Model\Cart');
    }

    public function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {

        } else {

        }
    }

    public function index() {
        $result = array();
        $data = $this->getData();
        $parameters = $data['params'];
        $controller = $data['controller'];
        $depositProductId = $this->scopeConfig->getValue('sales/preorder/deposit_product_id');
        $pre_order_coupon_code = $this->scopeConfig->getValue('sales/preorder/pre_order_coupon_code');

//        var_dump($this->_getQuote()->getId());
//        var_dump($parameters);die;
        if (isset($parameters['depositOrderId']) && $parameters['depositOrderId']) {
            $orderModel =  $this->simiObjectManager->create('Magento\Sales\Model\Order')
                ->loadByIncrementId($parameters['depositOrderId']);
            if ($orderModel && $orderModel->getId()) {
                $preOrderProducts = false;
                $orderData = $orderModel->toArray();
                $orderApiModel = $this->simiObjectManager->get('Simi\Simiconnector\Model\Api\Orders');
                $quoteApiModel = $this->simiObjectManager->get('Simi\Simiconnector\Model\Api\Quoteitems');
                $orderData['order_items']     = $orderApiModel->_getProductFromOrderHistoryDetail($orderModel);
                foreach ($orderData['order_items'] as $order_item) {
                    //var_dump($order_item);
                    if (
                        $order_item['product_id'] == $depositProductId &&
                        isset($order_item['product_options']['options']) && is_array($order_item['product_options']['options'])
                    ) {
                        foreach ($order_item['product_options']['options'] as $product_option) {
                            if (isset($product_option['label']) && $product_option['label'] == \Simi\Simicustomize\Model\Api\Quoteitems::PRE_ORDER_OPTION_TITLE) {
                                $preOrderProducts = json_decode(base64_decode($product_option['option_value']), true);
                                break;
                            }
                        }
                        break;
                    }
                }

                if ($preOrderProducts && is_array($preOrderProducts)) {
                    $cart = $this->_getCart();
                    //removeCart
                    $quoteItems = $this->_getQuote()->getItemsCollection();
                    foreach($quoteItems as $quoteItem)
                    {
                        $cart->removeItem($quoteItem->getId())->save();
                    }
                    //add product from order
                    foreach ($preOrderProducts as $preOrderProduct) {
                        $params = $preOrderProduct['request'];
                        $params['qty'] = $preOrderProduct['quantity'];
                        $params = isset($params)?$quoteApiModel->convertParams($params):array();
                        if (isset($params['qty'])) {
                            $filter        = $this->simiObjectManager
                                ->create('\Magento\Framework\Filter\LocalizedToNormalized', ['locale' => $this->simiObjectManager
                                    ->create('Magento\Framework\Locale\ResolverInterface')->getLocale()]);
                            $params['qty'] = $filter->filter($params['qty']);
                        }
                        $product               = $quoteApiModel->_initProduct($params['product']);
                        $cart->addProduct($product, $params);

                        $this->_getSession()->setCartWasUpdated(true);
                        $this->eventManager->dispatch(
                            'checkout_cart_add_product_complete',
                            ['product' => $product, 'request' => $controller->getRequest(),
                                'response' => $controller->getResponse()]
                        );

//                        var_dump($params);
//                        var_dump($product->getId());
//                        die;
                    }
                    $this->_getQuote()
                        ->setData('deposit_order_increment_id', $parameters['depositOrderId'])->save();
                    $this->_getQuote()->collectTotals()->save();
                }
//                var_dump($orderData);
//                var_dump($orderModel->getId());die;
                $result['startpreorderscompletes'] = [$orderData];
            }
        }
        return $result;
    }
}
