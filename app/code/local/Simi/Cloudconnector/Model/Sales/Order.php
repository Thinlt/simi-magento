<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @copyright   Copyright (c) 2015 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Connector Model Customer
 *
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @author      Simi Developer
 */
class Simi_Cloudconnector_Model_Sales_Order extends Simi_Cloudconnector_Model_Abstract
{

    /**
     * Internal constructor
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * get api result
     *
     * @param   array
     * @return   json
     */
    public function run($data)
    {
        $orderId = $data['orders'];
        $params = array();
        if (isset($data['params']))
            $params = $data['params'];
        if (!$orderId) {
            $offset = $data['offset'];
            $limit = $data['limit'];
            $update = $data['update'];
            $count = $data['count'];
            $information = $this->getListOrder($offset, $limit, $update, $count, $params);
        } else {
            $information = $this->getOrder($orderId);
        }
        return $information;
    }

    /**
     * get order collection
     *
     * @param   boolean
     * @return   object
     */
    public function getOrderCollection($update)
    {
        $collection = Mage::getModel('sales/order')->getCollection();
        if ($update) {
            $collection->getSelect()->join(array('sync' => $collection->getTable('cloudconnector/sync')),
                'main_table.entity_id = sync.element_id', array('*'));
            $collection->getSelect()->where('sync.type =' . self::TYPE_ORDER);
        }
        return $collection;
    }

    /**
     * get orders list
     *
     * @param   int , int, boolean, boolean, array
     * @return   json
     */
    public function getListOrder($offset, $limit, $update, $count, $params)
    {
        $orders = $this->getOrderCollection($update);
        if ($count)
            return $orders->getSize();
        if (!$offset)
            $offset = 0;
        if (!$limit)
            $limit = 10;
        $orders->setPageSize($limit);
        $orders->setCurPage($offset / $limit + 1);
        if ($params)
            foreach ($params as $key => $value) {
                $orders->addFieldToFilter($key, $value);
            }
        $orderList = array();
        foreach ($orders as $order) {
            $orderInfo = array();
            $orderInfo = $this->getOrderInfo($order);
            $orderList[] = $orderInfo;
            if ($update) {
                $this->removeUpdateRecord($order->getData('id'));
            }
        }
        return $orderList;
    }

    /**
     * get order information
     *
     * @param   int
     * @return   json
     */
    public function getOrder($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);;
        $orderInfo = $this->getOrderInfo($order);
        return array($orderInfo);
    }

    /**
     * get json information of an order
     *
     * @param   object
     * @return   json
     */
    public function getOrderInfo($order)
    {
        $orderInfo = array();
        $orderInfo['id'] = $order->getId();
        $orderInfo['order_currency_code'] = $order->getData('order_currency_code');
        $orderInfo['base_currency_code'] = $order->getData('base_currency_code');
        $orderInfo['store_currency_code'] = $order->getData('store_currency_code');
        $orderInfo['currency_template'] = Mage::helper('core')->currency(1000, true, false);
        $orderInfo['discount_amount'] = $order->getData('discount_amount');
        $orderInfo['tax_amount'] = $order->getData('tax_amount');
        $orderInfo['base_tax_amount'] = $order->getData('base_tax_amount');
        $orderInfo['tax_percent'] = '';
        $orderInfo['base_discount_amount'] = $order->getData('base_discount_amount');
        $orderInfo['base_shipping_amount'] = $order->getData('base_shipping_amount');
        $orderInfo['shipping_amount'] = $order->getData('shipping_amount');
        $orderInfo['base_shipping_amount'] = $order->getData('base_shipping_amount');
        $orderInfo['coupon'] = $order->getData('coupon_code') ? array($order->getData('coupon_code')) : array();
        $orderInfo['subtotal'] = $order->getData('subtotal');
        $orderInfo['base_subtotal'] = $order->getData('base_subtotal');
        $orderInfo['grand_total'] = $order->getData('grand_total');
        $orderInfo['base_grand_total'] = $order->getData('base_grand_total');
        $orderInfo['status'] = $order->getData('status');
        $orderInfo['created_at'] = $order->getData('created_at');
        $orderInfo['updated_at'] = $order->getData('updated_at');
        $orderInfo['quote_id'] = $order->getData('quote_id');
        $orderInfo['comments'] = $this->getOrderComments($order);
        $orderInfo['shipped_qty'] = $this->getShippedItems($order);
        $orderInfo['items'] = $this->getItems($order);
        if ($order->getData('customer_id')) {
            $orderInfo['customer'] = $this->getCustomerOrder($order);
        }
        if ($order->getBillingAddress()) {
            $orderInfo['billing_address'] = $this->getAddress($order->getBillingAddress());
            $orderInfo['bill_name'] = $order->getBillingAddress()->getData('firstname') . ' ' . $order->getBillingAddress()->getData('lastname');
        }
        if ($order->getShippingAddress()) {
            $orderInfo['shipping_address'] = $this->getAddress($order->getShippingAddress());
            if ($order->getShippingMethod()) {
                $orderInfo['shipping'] = $this->getOrderShippingMethod($order);
            }
        }
        if ($order->getPayment()) {
            $orderInfo['payment'] = $this->getPayment($order->getPayment());
        }
        return $orderInfo;
    }

    /**
     * get order customer
     *
     * @param   object
     * @return   json
     */
    public function getCustomerOrder($order)
    {
        $customerOrder = array();
        $customerOrder['customer_id'] = $order->getData('customer_id');
        $customerOrder['customer_email'] = $order->getData('customer_email');
        $customerOrder['customer_group_id'] = $order->getData('customer_group_id');
        $customerOrder['customer_first_name'] = $order->getData('customer_firstname');
        $customerOrder['customer_last_name'] = $order->getData('customer_lastname');
        $customerOrder['customer_name'] = $order->getData('customer_firstname') . ' ' . $order->getData('customer_lastname');
        return $customerOrder;
    }

    /**
     * get order address
     *
     * @param   object
     * @return   json
     */
    public function getAddress($address)
    {
        $addressOrder = array();
        if ($address->getData('country_id')) {
            $countryModel = Mage::getModel('directory/country')->loadByCode($address->getData('country_id'));
            $countryName = $countryModel->getName();
            $addressOrder['country'] = array(
                'code' => $address->getData('country_id'),
                'name' => $countryName
            );
        }
        $addressOrder['id'] = $address->getId();
        $addressOrder['first_name'] = $address->getData('firstname');
        $addressOrder['last_name'] = $address->getData('lastname');
        $addressOrder['phone'] = $address->getData('telephone');
        $stateCode = $address->getData('region_id') ? $address->getData('region_id') : '';
        $stateName = $address->getData('region') ? $address->getData('region') : '';
        $addressOrder['state'] = array(
            'code' => $stateCode,
            'name' => $stateName
        );
        $addressOrder['street'] = $address->getData('street');
        $addressOrder['city'] = $address->getData('city');
        $addressOrder['zip'] = $address->getData('postcode');
        return $addressOrder;
    }

    /**
     * get order payment
     *
     * @param   object
     * @return   json
     */
    public function getPayment($payment)
    {
        $code = $payment->getMethodInstance()->getCode();
        $title = $payment->getMethodInstance()->getTitle();
        return array(
            'title' => $title,
            'method_code' => $code
        );
    }

    /**
     * get order shipping method information
     *
     * @param   object
     * @return   json
     */
    public function getOrderShippingMethod($order)
    {
        return array(
            'cost' => $order->getData('shipping_amount'),
            'method_code' => $order->getShippingMethod(),
            'title' => $order->getShippingDescription(),
            'currency' => $order->getData('global_currency_code'),
            // 'shiping_tax_percent' => $order->getData('shiping_tax_percent')
        );
    }

    /**
     * get order items
     *
     * @param   object
     * @return   json
     */
    public function getItems($order)
    {
        $items = array();
        foreach ($order->getAllVisibleItems() as $item) {
            $itemInfo = $this->getInfoItems($item);
            $items[] = $itemInfo;
        }
        return $items;
    }

    /**
     * get order items
     *
     * @param   object
     * @return   json
     */
    public function getShippedItems($order)
    {
        $shippedItems = 0;
        foreach ($order->getAllVisibleItems() as $item) {
            if ($item->getData('product_type') == 'virtual' || $item->getData('product_type') == 'downloadable')
                $shippedItems++;
        }
        return $shippedItems;
    }

    /**
     * get item information
     *
     * @param   object
     * @return   json
     */
    public function getInfoItems($item)
    {
        $itemInfo = array();
        $itemInfo['id'] = $item->getId();
        $itemInfo['product_id'] = $item->getData('product_id');
        $itemInfo['product_type'] = $item->getData('product_type');
        $itemInfo['name'] = $item->getData('name');
        $itemInfo['sku'] = $item->getData('sku');
        $itemInfo['qty_ordered'] = $item->getData('qty_ordered');
        $itemInfo['price'] = $item->getData('price');
        $itemInfo['base_price'] = $item->getData('base_price');
        $itemInfo['tax_percent'] = $item->getData('tax_percent');
        $itemInfo['tax_amount'] = $item->getData('tax_amount');
        $itemInfo['row_total'] = $item->getData('row_total');
        $itemInfo['base_row_total'] = $item->getData('base_row_total');
        $itemInfo['virtual'] = $item->getData('is_virtual');
        if ($item->getData('product_type') == 'configurable') {
            $itemInfo['child_id'] = $this->getConfigurableChildId($item->getId());
        } elseif ($item->getData('product_type') == 'bundle') {
            $itemInfo['child_id'] = $this->getBundleChildId($item->getId());
        }
        // $itemInfo['options'] = $this->getOptionDetail($item->getOptions());
        return $itemInfo;
    }

    /**
     * get item information
     *
     * @param   object
     * @return   json
     */
    public function getConfigurableChildId($itemId)
    {
        $itemChild = Mage::getModel('sales/order_item')->load($itemId, 'parent_item_id');
        if ($itemChild->getId())
            return $itemChild->getProductId();
        return false;
    }

    /**
     * get item information
     *
     * @param   object
     * @return   json
     */
    public function getBundleChildId($itemId)
    {
        $childItems = Mage::getModel('sales/order_item')->getCollection()
            ->addFieldToFilter('parent_item_id', $itemId);
        $items = array();
        foreach ($childItems as $child) {
            $items[] = $child->getProductId();
        }
        return $items;
    }

    /**
     * get order comments
     *
     * @param   object
     * @return   json
     */
    public function getOrderComments($order)
    {
        $comments = array();
        foreach ($order->getAllStatusHistory() as $comment) {
            if ($comment->getId()) {
                $commentInfo = $this->getInfoCommetns($comment);
                $comments[] = $commentInfo;
            }
        }
        return $comments;
    }

    /**
     * get comment information
     *
     * @param   object
     * @return   json
     */
    public function getInfoCommetns($comment)
    {
        $commentInfo = array();
        $commentInfo['id'] = $comment->getId();
        $commentInfo['comment'] = $comment->getData('comment') ? $comment->getData('comment') : '';
        $commentInfo['is_customer_notified'] = $comment->getData('is_customer_notified');
        $commentInfo['status'] = $comment->getData('status') ? $comment->getData('status') : '';
        $commentInfo['created_at'] = $comment->getData('created_at');
        $commentInfo['updated_at'] = $comment->getData('created_at');
        return $commentInfo;
    }

    /**
     * pull data from cloud
     *
     * @param   array
     * @return
     */
    public function pull($data)
    {
        $orderId = $data['id'];
        if ($orderId) {
            return $this->updateOrder($data);
        } else {
            $order = Mage::getModel('cloudconnector/sales_order_create');
            $order = $order->createOrder($data);
            return $order;
        }
    }


    /**
     * update order
     * @param   json
     * @return
     */
    public function updateOrder($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $order->setData($order);
        try {
            $order->save();
            return $order->getData();
        } catch (Exception $e) {

        }
    }
}