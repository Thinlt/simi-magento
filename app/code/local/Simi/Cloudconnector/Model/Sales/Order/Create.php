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
class Simi_Cloudconnector_Model_Sales_Order_Create extends Mage_Core_Model_Abstract
{



    /**
     * Retrieve order create model
     *
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }



    public function createOrder($data)
    {
        $quote = Mage::getModel('sales/quote')
            ->setStoreId(Mage::app()->getStore('default')->getId());
        // Assign Customer To Sales Order Quote
        $customer = $this->setCustomer($data['customer']);
        $quote->assignCustomer($customer);

        foreach ($data['items'] as $item) {
            $product = Mage::getModel('catalog/product')->load($item['id']);
            $quote->addProduct($product, new Varien_Object($item['varien']));
        }


        // add product(s)
//        $product = Mage::getModel('catalog/product')->load($data);
//        $buyInfo = array(
//            'qty' => 1,
////            'options' => array(    // custom options
////                7 => 4,
////                8 => 6
//            // option_id => value_id
////            ),
//            'super_attribute' => array(   // configurable product
//                186 => 97,
//                // attribute_id => value_id
//            ),
//        );
//        $quote->addProduct($product, new Varien_Object($buyInfo));


        $billingAddress = $quote->getBillingAddress()->addData($data['billing_address']);
        $shippingAddress = $quote->getShippingAddress()->addData($data['shipping_address']);
        $shippingAddress->setCollectShippingRates(true)->collectShippingRates()
            ->setShippingMethod('simi_shipping')
            ->setPaymentMethod('simi_payment');
        $quote->getPayment()->importData(array('method' => 'simi_payment'));
        $quote->save();
        $service = Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();
        $order = $service->getOrder();

        $order->setShippingAmount($data['shipping_amount']);
        $order->setState($data['status']);
        $order->setStatus($data['status']);
        $order->setsubtotal($data['subtotal']);
        $order->setGrandTotal($data['grand_total']);
        $order->setDiscountAmount($data['discount_amount']);
        $order->setTaxAmount($data['tax_amount']);
        $order->setPaymentDescription($data['payment']['title']);
        //$order->setShippingDescription('Free Ship');
        $order->save();
        // Resource Clean-Up
        if (isset($data['paid_amount']))
            $this->invoiceOrder($order->getId());
        $quote = $customer = $service = null;
        return ['order_id' => $order->getId()];
    }

    public function invoiceOrder($order_id)
    {
        $order = Mage::getModel("sales/order")->load($order_id);
        try {
            if (!$order->canInvoice()) {
                return;
            }
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
            if (!$invoice->getTotalQty()) {
                return;
            }
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transactionSave->save();
        } catch (Mage_Core_Exception $e) {

        }
    }


    public function setCustomer($data)
    {
        $customer = Mage::getModel('customer/customer')
            ->setWebsiteId($this->getStoreId())
            ->loadByEmail($data['email']);
        if (empty($customer->getId())) {
            $customer = Mage::getModel('customer/customer');
            $customer = $customer->setWebsiteId($this->getStoreId())
                ->setFirstname($data['first_name'])
                ->setLastname($data['last_name'])
                ->setEmail($data['email'])
                ->setPassword("simi@123");
            $customer = $customer->save();
        }
        return $customer;
    }

    public function getStoreId()
    {
        return Mage::app()->getStore('default')->getId();
    }
}