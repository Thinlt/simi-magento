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
        if (isset($parameters['depositOrderId']) && $parameters['depositOrderId']) {
            $orderModel =  $this->simiObjectManager->create('Magento\Sales\Model\Order')
                ->loadByIncrementId($parameters['depositOrderId']);
            if ($orderModel && $orderModel->getId()) {
                $quoteApiModel = $this->simiObjectManager->get('Simi\Simiconnector\Model\Api\Quoteitems');
                $preOrderProducts = $this->simiObjectManager->get('\Simi\Simicustomize\Helper\SpecialOrder')
                    ->getPreOrderProductsFromOrder($orderModel);
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
                    }
                    $this->_getQuote()
                        ->setData('deposit_order_increment_id', $parameters['depositOrderId'])->save();
                    try {
                        $this->_getQuote()->collectTotals()->save();
                    } catch (\Exception $e) {

                    }
                }
                $result['startpreorderscompletes'] = array('status' => 'success');
            }
        }
        return $result;
    }
}
