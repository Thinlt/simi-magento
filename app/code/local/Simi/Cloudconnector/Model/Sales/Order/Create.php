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

    private $_storeId = '1';
    private $_groupId = '1';
    private $_sendConfirmation = '0';

    private $orderData = array();
    private $_product;

    private $_sourceCustomer;
    private $_sourceOrder;

    public function setOrderInfo(Varien_Object $sourceOrder, Mage_Customer_Model_Customer $sourceCustomer, $productId)
    {
        $this->_sourceOrder = $sourceOrder;
        $this->_sourceCustomer = $sourceCustomer;

        //You can extract/refactor this if you have more than one product, etc.
        $this->_product = Mage::getModel('catalog/product')->load($productId);
//        print_r($this->_product ); die;
        $this->_sourceCustomer = Mage::getModel('customer/customer')->load(136);

//        print_r(get_class_methods($this->_sourceCustomer->getDefaultShippingAddress()->getId)); die;

        $this->_sourceOrder = Mage::getModel('sales/order')->load(187);
        $this->orderData = array(
            'session' => array(
                'customer_id' => $this->_sourceCustomer->getId(),
                'store_id' => $this->_storeId,
            ),
            'payment' => array(
                'method' => 'cashondelivery',
            ),
            'add_products' => array(
                $this->_product->getId() => array('qty' => 1),
            ),
            'order' => array(
//                'currency' => $this->_sourceOrder->getData('global_currency_code'),
                'currency' => 'USD',
                'account' => array(
                    'group_id' => $this->_groupId,
                    'email' => $this->_sourceCustomer->getEmail()
                ),
                //$this->_sourceCustomer->getDefaultShippingAddress()
                'billing_address' => array(
                    'customer_address_id' => $this->_sourceCustomer->getDefaultBillingAddress()->getId(),
                    'prefix' => $this->_sourceCustomer->getDefaultBillingAddress()->getPrefix(),
                    'firstname' => $this->_sourceCustomer->getDefaultBillingAddress()->getFirstname(),
                    'middlename' => $this->_sourceCustomer->getDefaultBillingAddress()->getMiddlename(),
                    'lastname' => $this->_sourceCustomer->getDefaultBillingAddress()->getLastname(),
                    'suffix' => $this->_sourceCustomer->getDefaultBillingAddress()->getSuffix(),
                    'company' => $this->_sourceCustomer->getDefaultBillingAddress()->getCompany(),
                    'street' => array($this->_sourceCustomer->getDefaultBillingAddress()->getStreet(), ''),
                    'city' => $this->_sourceCustomer->getDefaultBillingAddress()->getCity(),
                    'country_id' => $this->_sourceCustomer->getDefaultBillingAddress()->getCountryId(),
                    'region' => $this->_sourceCustomer->getDefaultBillingAddress()->getRegion(),
                    'region_id' => $this->_sourceCustomer->getDefaultBillingAddress()->getRegionId(),
                    'postcode' => $this->_sourceCustomer->getDefaultBillingAddress()->getPostcode(),
                    'telephone' => $this->_sourceCustomer->getDefaultBillingAddress()->getTelephone(),
                    'fax' => $this->_sourceCustomer->getDefaultBillingAddress()->getFax(),
                ),
                'shipping_address' => array(
                    'customer_address_id' => $this->_sourceCustomer->getDefaultShippingAddress()->getId(),
                    'prefix' => $this->_sourceCustomer->getDefaultShippingAddress()->getPrefix(),
                    'firstname' => $this->_sourceCustomer->getDefaultShippingAddress()->getFirstname(),
                    'middlename' => $this->_sourceCustomer->getDefaultShippingAddress()->getMiddlename(),
                    'lastname' => $this->_sourceCustomer->getDefaultShippingAddress()->getLastname(),
                    'suffix' => $this->_sourceCustomer->getDefaultShippingAddress()->getSuffix(),
                    'company' => $this->_sourceCustomer->getDefaultShippingAddress()->getCompany(),
                    'street' => array($this->_sourceCustomer->getDefaultShippingAddress()->getStreet(), ''),
                    'city' => $this->_sourceCustomer->getDefaultShippingAddress()->getCity(),
                    'country_id' => $this->_sourceCustomer->getDefaultShippingAddress()->getCountryId(),
                    'region' => $this->_sourceCustomer->getDefaultShippingAddress()->getRegion(),
                    'region_id' => $this->_sourceCustomer->getDefaultShippingAddress()->getRegionId(),
                    'postcode' => $this->_sourceCustomer->getDefaultShippingAddress()->getPostcode(),
                    'telephone' => $this->_sourceCustomer->getDefaultShippingAddress()->getTelephone(),
                    'fax' => $this->_sourceCustomer->getDefaultShippingAddress()->getFax(),
                ),
                'shipping_method' => 'ups_1DA',
                'payment_method' => 'cashondelivery',
                'comment' => array(
                    // 'customer_note' => $this->_sourceOrder->getComment(),
                    'customer_note' => 'sss',
                ),
                'send_confirmation' => $this->_sendConfirmation
            )
        );
        print_r($this->orderData);
        die;
    }

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
     * Initialize order creation session data
     *
     * @param array $data
     * @return Mage_Adminhtml_Sales_Order_CreateController
     */
    protected function _initSession($data)
    {
        /* Get/identify customer */
        if (!empty($data['customer_id'])) {
            $this->_getSession()->setCustomerId((int)$data['customer_id']);
        }

        /* Get/identify store */
        if (!empty($data['store_id'])) {
            $this->_getSession()->setStoreId((int)$data['store_id']);
        }

        return $this;
    }

    /**
     * Creates order
     */
    public function create()
    {
        $orderData = $this->orderData;
        if (!empty($orderData)) {
            $this->_initSession($orderData['session']);
            try {
                $this->_processQuote($orderData);
                if (!empty($orderData['payment'])) {
                    $this->_getOrderCreateModel()->setPaymentData($orderData['payment']);
                    $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($orderData['payment']);
                }
                //  $item = $this->_getOrderCreateModel()->getQuote()->getItemByProduct($this->_product);
//                $item->addOption(new Varien_Object(
//                    array(
//                        'product' => $this->_product,
//                        'code' => 'option_ids',
//                        'value' => '5' /* Option id goes here. If more options, then comma separate */
//                    )
//                ));

                Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "0");
                $_order = $this->_getOrderCreateModel()
                    ->importPostData($orderData['order']);
                $_order = $_order->createOrder();
                $this->_getSession()->clear();
                Mage::unregister('rule_data');
                return $_order;
            } catch (Exception $e) {
                Mage::log("Order save error...");
            }
        }
        return 111;
    }

    protected function _processQuote($data = array())
    {
        /* Saving order data */
        if (!empty($data['order'])) {
            $this->_getOrderCreateModel()->importPostData($data['order']);
        }
        $this->_getOrderCreateModel()->getBillingAddress();
        $this->_getOrderCreateModel()->setShippingAsBilling(true);
        /* Just like adding products from Magento admin grid */
        if (!empty($data['add_products'])) {
            $this->_getOrderCreateModel()->addProducts($data['add_products']);
        }
        /* Collect shipping rates */
        $this->_getOrderCreateModel()->collectShippingRates();
        /* Add payment data */
        if (!empty($data['payment'])) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
        }
        $this->_getOrderCreateModel()
            ->initRuleData()
            ->saveQuote();

        if (!empty($data['payment'])) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
        }
        return $this;
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
            ->setShippingMethod('ups_1DA')
            ->setPaymentMethod('cashondelivery');
        $quote->getPayment()->importData(array('method' => 'cashondelivery'));
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