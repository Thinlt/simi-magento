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


    /**
     * create order from simi cloud
     * @param $data
     * @return array
     */
    public function createOrder($data)
    {
        $quote = Mage::getModel('sales/quote')
            ->setStoreId(Mage::app()->getStore('default')->getId());
        // Assign Customer To Sales Order Quote
        $customer = $this->setCustomer($data['customer']);
        $quote->assignCustomer($customer);

        foreach ($data['items'] as $item) {
            $product = Mage::getModel('catalog/product')->load($item['id']);
            if (isset($item['varien']['super_attribute']))
                $item['varien']['super_attribute'] = $this->setConfigable($item['varien']['super_attribute']);
            $quote->addProduct($product, new Varien_Object($item['varien']));
        }


        $billingAddress = $quote->getBillingAddress()->addData($data['billing_address']);
        $shippingAddress = $quote->getShippingAddress()->addData($data['shipping_address']);
        $shippingAddress->setCollectShippingRates(true)->collectShippingRates()
//            ->setShippingMethod('ups_1DA')
            ->setShippingMethod('simi_shipping_simi_shipping')
//            ->setPaymentMethod('cashondelivery');
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
        $order->save();
        // Resource Clean-Up
        if (isset($data['paid_amount']))
            $this->invoiceOrder($order->getId());
        $quote = $customer = $service = null;
        return ['order_id' => $order->getId()];
    }

    /**
     * invoice order from simi cloud
     * @param $order_id
     */
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


    /**
     * set customer to order | create or find
     * @param $data
     * @return false|Mage_Core_Model_Abstract
     */
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

    /**
     * get id product attribute by code and values
     * @param $attribute_id
     * @param $label
     * @return string
     */
    function getOptionId($attribute_id, $label)
    {
        $attribute_model = Mage::getModel('eav/entity_attribute');
        $attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');
        $attribute = $attribute_model->load($attribute_id);
        $attribute_table = $attribute_options_model->setAttribute($attribute);
        $options = $attribute_options_model->getAllOptions(false);
        $optionId = '';
        foreach ($options as $option) {
            if ($option['label'] == $label) {
                $optionId = $option['value'];
                break;
            }
        }
        return $optionId;
    }

    /**
     * @param $data
     * @return array
     */
    public function setConfigable(&$data)
    {
        $convert = [];
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $convert[$key] = $this->getOptionId($key, $val);
            }
        }
        return $convert;
    }
}