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
 * Cloudcontroler Controller Action
 *
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @author      Simi Developer
 */
abstract class Simi_Cloudconnector_Controller_Action extends Mage_Core_Controller_Front_Action
{

    protected $_data;

    /**
     * get data
     *
     * @return  array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * set data
     *
     * @param  array
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * get cloudconnector helper
     *
     * @param
     * @return  Simi_Cloudconnector_Helper_Data
     */
    public function _helper($data)
    {
        return Mage::helper('cloudconnector');
    }

    /**
     * check connection
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $enable = $this->_helper()->getConfig('enable', Mage::app()->getWebsite()->getId());
        $enable = 1;
        if (!$enable) {
            header("HTTP/1.0 503");
            echo 'Connect was disable!';
            exit();
        }

        if (!$this->isCheckKey()) {
            header("HTTP/1.0 401 Unauthorized");
            echo 'Connect error!';
            exit();
        }
        $data = $this->getRequest()->getParams();
        $this->releaseData($data);
    }


    /**
     * convert json to array
     *
     * @param    array
     * @return   array
     */
    public function releaseData($data)
    {
        $this->setData($data);
        $this->eventChangeData($this->getEventName(), $data);
        $this->_data = $this->getData();
    }

    /**
     * convert data to Json
     *
     * @param    array
     * @return   json
     */
    public function convertToJson($data)
    {
        $this->setData($data);
        $this->eventChangeData($this->getEventName('_return'), $data);
        $this->_data = $this->getData();
        return Mage::helper('core')->jsonEncode($this->_data);
    }

    /**
     * print json from array
     *
     * @param    array
     * @return
     */
    public function _printDataJson($data)
    {
        ob_start();
        echo $this->convertToJson($data);
        header("Content-Type: application/json");
        exit();
        ob_end_flush();
    }

    /**
     * check head key
     *
     * @param
     * @return   boolean
     */
    public function isCheckKey()
    {
        if (!function_exists('getallheaders')) {

            function getallheaders()
            {
                $head = array();
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $head[$name] = $value;
                    } else if ($name == "CONTENT_TYPE") {
                        $head["Content-Type"] = $value;
                    } else if ($name == "CONTENT_LENGTH") {
                        $head["Content-Length"] = $value;
                    }
                }
                return $head;
            }

        }
        $head = getallheaders();
        // token is key
        $secretKey = $this->_helper()->getConfig('api_key', Mage::app()->getWebsite()->getId());
        $token;

//        foreach ($head as $k => $h) {
//            if ($k == "Data") {
//                $data = $h;
//            }if ($k == "Authorization") {
//                $authorization = $h;
//            }
//        }
        $request = new Zend_Controller_Request_Http();
        $data = $request->getHeader('Data');
        $authorization = $request->getHeader('Authorization');
        $sign = $request->getHeader('Sign');
        $signature = base64_encode(hash_hmac('sha256', json_encode($data), $secretKey, true));

        if ($authorization == $signature || $sign == $signature) {
            return true;
        } else {
            return false;    // need change
        }
    }

    /**
     * dispatch event
     *
     * @param   string , array
     * @return
     */
    public function eventChangeData($event_name, $data)
    {
        Mage::dispatchEvent($event_name, array('object' => $this, 'data' => $data));
    }

    /**
     * get event name
     *
     * @param   string
     * @return  string
     */
    public function getEventName($last = '')
    {
        return $this->getFullActionName() . $last;
    }

}


