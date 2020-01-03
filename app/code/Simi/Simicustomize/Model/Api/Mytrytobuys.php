<?php

namespace Simi\Simicustomize\Model\Api;

class Mytrytobuys extends \Simi\Simiconnector\Model\Api\Apiabstract
{
    public $time_zone = null;
    public function setBuilderQuery()
    {
        $data = $this->getData();
        $tryToByProductId = $this->scopeConfig->getValue('sales/trytobuy/trytobuy_product_id');
        $orderItems = $this->simiObjectManager->create('Magento\Sales\Model\Order\Item')->getCollection()
            ->addFieldToFilter('product_id', $tryToByProductId)
        ;
        $orderIdArray = array();
        foreach ($orderItems as $orderItem) {
            $orderIdArray[] = $orderItems->getData('order_id');
        }

        if ($data['resourceid']) {
            $this->builderQuery = $this->simiObjectManager->create('Magento\Sales\Model\Order')
                ->loadByIncrementId($data['resourceid']);
        } else {
            $this->builderQuery = $this->simiObjectManager->create('Magento\Sales\Model\Order')->getCollection()
                    ->addFieldToFilter(
                        'customer_id',
                        $this->simiObjectManager->create('Magento\Customer\Model\Session')->getCustomer()->getId()
                    )
                    ->addFieldToFilter(
                        'entity_id',
                        array('in' => $orderIdArray)
                    )
                    ->setOrder('entity_id', 'DESC');
        }
    }

    public function index()
    {
        $result   = parent::index();
        $customer = $this->simiObjectManager->create('Magento\Customer\Model\Session')->getCustomer();
        foreach ($result['mytrytobuys'] as $index => $order) {
            $this->_updateOrderInformation($order, $customer);
            $result['mytrytobuys'][$index] = $order;
        }
        return $result;
    }

    private function _updateOrderInformation(&$order, $customer)
    {
        $orderModel               = $this->simiObjectManager
            ->create('Magento\Sales\Model\Order')->load($order['entity_id']);
        $order['payment_method']  = $orderModel->getPayment()->getMethodInstance()->getTitle();
        $order['shipping_method'] = $orderModel->getShippingDescription();
        $order['billing_address'] = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Address')
            ->getAddressDetail($orderModel->getBillingAddress(), $customer);
        if (!$orderModel->getShippingAddress()) {
            $order['shipping_address'] = $order['billing_address'];
        } else {
            $order['shipping_address'] = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Address')
                ->getAddressDetail($orderModel->getShippingAddress(), $customer);
        }
        $order['billing_address'] = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Address')
            ->getAddressDetail($orderModel->getBillingAddress(), $customer);
        if (!$this->time_zone) {
            $this->time_zone = $this->simiObjectManager->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        }
        $order['created_at']      = $this->time_zone->date($order['created_at'])->format('Y-m-d H:i:s');
        $order['updated_at']      = $this->time_zone->date($order['updated_at'])->format('Y-m-d H:i:s');
    }
}
