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
 * @category    Magestore
 * @package    Magestore_Cloudconnector
 * @copyright    Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license    http://www.magestore.com/license-agreement.html
 */

/**
 * Cloudconnector Observer Model
 *
 * @category    Magestore
 * @package    Magestore_Cloudconnector
 * @author    Magestore Developer
 */
class Simi_Cloudconnector_Model_Observer
{
    const TYPE_CUSTOMER_GROUP = 1;
    const TYPE_CUSTOMER = 2;
    const TYPE_CATALOG_CATEGORY = 3;
    const TYPE_ATTRIBUTE = 4;
    const TYPE_ATTRIBUTESET = 5;
    const TYPE_PRODUCT = 6;
    const TYPE_QUOTE = 7;
    const TYPE_ORDER = 8;
    const TYPE_INVOICE = 9;
    const TYPE_SHIPMENT = 10;
    const TYPE_CREDITMEMO = 11;

    /**
     * return web hook url
     * @return string
     */
    public function getUrl()
    {
//        return 'http://requestb.in/1028ysp1';
        return Mage::getStoreConfig('cloudconnector/general/web_hook_simi');
    }

    /**
     * return api - key
     * @return string
     */
    public function getKey()
    {
        return $secretKey = $this->_helper('data')->getConfig('api_key');
    }

    public function checkWebHook($source)
    {
        if ($this->_helper('data')->getConfig('enable') == 1 && $this->_helper('data')->getConfig('web_hook') == 1) {
            if ($this->_helper('data')->getConfig($source) == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * get cloudconnector helper
     *
     * @param
     * @return  Simi_Cloudconnector_Helper_Data
     */
    public function _helper($data)
    {
        return Mage::helper('cloudconnector/' . $data);
    }

    /**
     * process save data to simicart_cloud_update table
     *
     * @param int , int
     * @return
     */
    public function saveDataToSync($id, $type)
    {
        return;
        $sync = Mage::getModel('cloudconnector/sync')->getCollection()
            ->addFieldToFilter('element_id', $id)
            ->addFieldToFilter('type', $type);
        if ($sync->getSize() > 0) {
            $syncExisted = $sync->getFirstItem();
            $syncExisted->setData('status', 1)
                ->setData('update_time', now());
            try {
                $syncExisted->save();
            } catch (Exception $e) {
            }
        } else {
            $syncNew = Mage::getModel('cloudconnector/sync');
            $syncNew->setData('element_id', $id)
                ->setData('type', $type)
                ->setData('status', 1)
                ->setData('created_time', now())
                ->setData('update_time', now());
            try {
                $syncNew->save();
            } catch (Exception $e) {
            }
        }
    }

    /**
     * customer_group_save_after event
     *
     * @param observer
     * @return
     */
    public function customerGroupSaveAfter($observer)
    {
        $customerOb = $observer['data_object'];
        $customerGroupId = $customerOb->getData('customer_group_id');
        if ($customerGroupId >= 0) {
            if ($this->checkWebHook('web_hook_customer')) {
                $group = Mage::getModel('cloudconnector/customer_group');
                $group_data = $group->getCustomerGroup($customerGroupId);
                $this->_helper('call')->sendRequest('POST', $this->getUrl(), $group_data, true, 'group.created', $this->getKey());
            }
            $this->saveDataToSync($customerGroupId, self::TYPE_CUSTOMER_GROUP);
        }
    }

    /**
     * customer_save_after event
     *
     * @param observer
     * @return
     */
    public function customerSaveAfter($observer)
    {
        $customerId = $observer->getCustomer()->getId();
        if ($customerId) {
            if ($this->checkWebHook('web_hook_customer')) {
                $customer = Mage::getModel('cloudconnector/customer');
                $data = $customer->getCustomer($customerId);
                $this->_helper('call')->sendRequest('POST', $this->getUrl(), $data, true, 'customer.created', $this->getKey());
            }
            $this->saveDataToSync($customerId, self::TYPE_CUSTOMER);
        }
    }

    /**
     * catalog_category_save_after event
     *
     * @param observer
     * @return
     */
    public function categorySaveAfter($observer)
    {
        $categoryId = $observer->getCategory()->getId();
        if ($categoryId) {
            if ($this->checkWebHook('web_hook_product')) {
                $category = Mage::getModel('cloudconnector/catalog_category');
                $data = $category->getCategoryInfo($categoryId);
                $this->_helper('call')->sendRequest('POST', $this->getUrl(), $data, true, 'category.created', $this->getKey());
            }
            $this->saveDataToSync($categoryId, self::TYPE_CATALOG_CATEGORY);
        }
    }

    /**
     * catalog_entity_attribute_save_after event
     *
     * @param observer
     * @return
     */
    public function attributeSaveAfter($observer)
    {
        $attributeId = $observer->getAttribute()->getId();
        if ($attributeId) {
            if ($this->checkWebHook('web_hook_product')) {
                $attribute = Mage::getModel('cloudconnector/Attributes');
                $data = $attribute->getAttribute($attributeId);
                $this->_helper('call')->sendRequest('POST', $this->getUrl(), $data, true, 'attribute.created', $this->getKey());
            }
            $this->saveDataToSync($attributeId, self::TYPE_ATTRIBUTE);
        }
    }

    /**
     * eav_entity_attribute_set_save_after event
     *
     * @param observer
     * @return
     */
    public function attributeSetSaveAfter($observer)
    {
        $attributesetId = $observer['data_object']->getData('attribute_set_id');
        if ($attributesetId) {
            if ($this->checkWebHook('web_hook_product')) {
                $attributeset = Mage::getModel('cloudconnector/Attributesets');
                $data = $attributeset->getAttributeset($attributesetId);
                $this->_helper('call')->sendRequest('POST', $this->getUrl(), $data, true, 'attributeset.created', $this->getKey());
            }
            $this->saveDataToSync($attributesetId, self::TYPE_ATTRIBUTESET);
        }
    }

    /**
     * catalog_product_save_after event
     *
     * @param observer
     * @return
     */
    public function productSaveAfter($observer)
    {
        $productId = $observer->getProduct()->getId();
        if ($productId) {
            if ($this->checkWebHook('web_hook_product')) {
                $product = Mage::getModel('cloudconnector/catalog_product');
                $data = $product->getProductInfo($productId);
                $this->_helper('call')->sendRequest('POST', $this->getUrl(), $data, true, 'product.created', $this->getKey());
            }
            $this->saveDataToSync($productId, self::TYPE_PRODUCT);
        }
    }

    /**
     * sales_order_save_after event
     *
     * @param observer
     * @return
     */
    public function orderSaveAfter($observer)
    {
        $order = $observer->getOrder();
        $orderId = $order->getId();
        $shipping = $order->getShippingMethod();

        if ($orderId && $shipping != 'simi_shipping_simi_shipping') {
            echo 111;
            if ($this->checkWebHook('web_hook_order')) {
                $order = Mage::getModel('cloudconnector/sales_order');
                $data = $order->getOrder($orderId);
                $this->_helper('call')->sendRequest('POST', $this->getUrl(), $data, true, 'order.created', $this->getKey());
            }
            $this->saveDataToSync($orderId, self::TYPE_ORDER);
        }
    }

    /**
     * sales_order_invoice_save_after event
     *
     * @param observer
     * @return
     */
    public function invoiceSaveAfter($observer)
    {
        $invoiceId = $observer->getInvoice()->getId();
        if ($invoiceId) {
            if ($this->checkWebHook('web_hook_order')) {
                $invoice = Mage::getModel('cloudconnector/sales_order_invoice');
                $data = $invoice->getInvoice($invoiceId);
                $this->_helper('call')->sendRequest('POST', $this->getUrl(), $data, true, 'invoice.created', $this->getKey());
            }
            $this->saveDataToSync($invoiceId, self::TYPE_INVOICE);
        }
    }

    /**
     * sales_order_shipment_save_after event
     *
     * @param observer
     * @return
     */
    public function shipmentSaveAfter($observer)
    {
        $shipmentId = $observer->getShipment()->getId();
        if ($shipmentId) {
            if ($this->checkWebHook('web_hook_order')) {
                $shipment = Mage::getModel('cloudconnector/sales_order_shipment');
                $data = $shipment->getShipment($shipmentId);
                $this->_helper('call')->sendRequest('POST', $this->getUrl(), $data, true, 'shipping.created', $this->getKey());
            }
            $this->saveDataToSync($shipmentId, self::TYPE_SHIPMENT);
        }
    }

    /**
     * sales_order_creditmemo_save_after event
     *
     * @param observer
     * @return
     */
    public function creditmemoSaveAfter($observer)
    {
        $creditmemoId = $observer->getCreditmemo()->getId();
        if ($creditmemoId) {
            if ($this->checkWebHook('web_hook_order')) {
                $creditmemo = Mage::getModel('cloudconnector/sales_order_shipment');
                $data = $creditmemo->getCreditmemo($creditmemoId);
                $this->_helper('call')->sendRequest('POST', $this->getUrl(), $data, true, 'creditmemo.created', $this->getKey());
            }
            $this->saveDataToSync($creditmemoId, self::TYPE_CREDITMEMO);
        }
    }


    /**
     * hide shpping method
     * @param Varien_Event_Observer $observer
     */
    public function hideShippingMethods(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $store = Mage::app()->getStore($quote->getStoreId());
        $carriers = Mage::getStoreConfig('carriers', $store);
        $hiddenMethodCode = 'simi_shipping';

        foreach ($carriers as $carrierCode => $carrierConfig) {
            if ($carrierCode == $hiddenMethodCode) {
                $store->setConfig("carriers/{$carrierCode}/active", '0');
            }
        }
    }

    /**
     * hide shipping
     * @param $observer
     */
    public function paymentMethodIsActive($observer)
    {
        $result = $observer['result'];
        $method = $observer['method_instance'];
        if ($method->getCode() == 'simi_payment') {
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Cloudconnector') {
                $result->isAvailable = false;
            }
        }
    }

}