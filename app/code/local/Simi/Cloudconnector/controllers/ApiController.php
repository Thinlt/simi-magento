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
 * @package    Simi_Cloudconnector
 * @copyright    Copyright (c) 2015 Magestore (http://www.magestore.com/)
 * @license    http://www.magestore.com/license-agreement.html
 */

/**
 * Connector Config Controller
 *
 * @category    Simi
 * @package    Simi_Cloudconnector
 * @author    Simi Developer
 */
class Simi_Cloudconnector_ApiController extends Simi_Cloudconnector_Controller_Action
{

    /**
     * call synchronization api model (magento -> cloud)
     *
     * @param    array
     * @return   json
     */
    public function restAction()
    {
        $data = $this->getData();
        $model = $this->getApiModel($data);
        Mage::dispatchEvent('cloudconnector_rest_model',
            array('model' => $model)
        );
        $information = $model->run($data);
        $this->_printDataJson($information);
    }

    /**
     * call synchronization api model (cloud -> magento)
     *
     * @param    array
     * @return   json
     */
    public function syncAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getData();
            $model = $this->getApiModel($data);
            Mage::dispatchEvent('cloudconnector_sync_model',
                array('model' => $model)
            );
            $json = json_decode(file_get_contents('php://input'), true);
            if ($json)
                $information = $model->pull($json);
            else
                $information = array();
            $this->_printDataJson($information);
        } else
            $this->_printDataJson(array('message' => 'post only accept!'));
    }

    public function getApiModel($data)
    {
        if (isset($data['customer-groups'])) {
            $model = Mage::getModel('cloudconnector/customer_group');
        } else if (isset($data['categories'])) {
            $model = Mage::getModel('cloudconnector/catalog_category');
        } else if (isset($data['customers'])) {
            $model = Mage::getModel('cloudconnector/customer');
        } else if (isset($data['attributes'])) {
            $model = Mage::getModel('cloudconnector/attributes');
        } else if (isset($data['attributesets'])) {
            $model = Mage::getModel('cloudconnector/attributesets');
        } else if (isset($data['products'])) {
            $model = Mage::getModel('cloudconnector/catalog_product');
        } else if (isset($data['quotes'])) {
            $model = Mage::getModel('cloudconnector/sales_quote');
        } else if (isset($data['orders'])) {
            $model = Mage::getModel('cloudconnector/sales_order');
        } else if (isset($data['invoices'])) {
            $model = Mage::getModel('cloudconnector/sales_order_invoice');
        } else if (isset($data['shipments'])) {
            $model = Mage::getModel('cloudconnector/sales_order_shipment');
        } else if (isset($data['creditmemos'])) {
            $model = Mage::getModel('cloudconnector/sales_order_creditmemo');
        }

        return $model;
    }

    public function save_setting_webhookAction(){
        $web_hook = $this->getRequest()->getParam('web_hook');
        $web_hook = str_replace(' ','+',$web_hook);
        Mage::getConfig()->saveConfig('cloudconnector/general/web_hook_simi', $web_hook);
        Mage::getConfig()->saveCache();
        $information = array('status' => 'SUCCESS');
        $this->_printDataJson($information);
    }

    public function get_week_hook_urlAction(){
        echo Mage::getStoreConfig('cloudconnector/general/web_hook_simi');
        exit();
    }
}