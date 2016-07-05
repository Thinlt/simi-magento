<?php

/**
 * Simi
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Simi.com license that is
 * available through the world-wide-web at this URL:
 * http://www.simi.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @copyright   Copyright (c) 2012 Simi (http://www.simi.com/)
 * @license     http://www.simi.com/license-agreement.html
 */

/**
 * Simirewardpoints Calculation Helper Abstract
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Helper_Calculation_Abstract extends Mage_Core_Helper_Abstract {

    /**
     * Cache helper data to Memory
     * 
     * @var array
     */
    protected $_cacheRule = array();

    /**
     * check cache is existed or not
     * 
     * @param string $cacheKey
     * @return boolean
     */
    public function hasCache($cacheKey) {
        if (array_key_exists($cacheKey, $this->_cacheRule)) {
            return true;
        }
        return false;
    }

    /**
     * save value to cache
     * 
     * @param string $cacheKey
     * @param mixed $value
     * @return Simi_Simirewardpoints_Helper_Calculation_Abstract
     */
    public function saveCache($cacheKey, $value = null) {
        $this->_cacheRule[$cacheKey] = $value;
        return $this;
    }

    /**
     * get cache value by cache key
     * 
     * @param  $cacheKey
     * @return mixed
     */
    public function getCache($cacheKey) {
        if (array_key_exists($cacheKey, $this->_cacheRule)) {
            return $this->_cacheRule[$cacheKey];
        }
        return null;
    }

    /**
     * get customer group id, depend on current checkout session (admin, frontend)
     * 
     * @return int
     */
    public function getCustomerGroupId() {
        if (!$this->hasCache('abstract_customer_group_id')) {
            if (Mage::app()->getStore()->isAdmin()) {
                $customer = Mage::getSingleton('adminhtml/session_quote')->getCustomer();
                $this->saveCache('abstract_customer_group_id', $customer->getGroupId());
            } else {
//                if (Mage::getConfig()->getModuleConfig('Simi_Webpos')->is('active', 'true') && !Mage::getSingleton('customer/session')->getCustomerGroupId()) {
//
//                    $this->saveCache('abstract_customer_group_id', Mage::getModel('customer/customer')->load(Mage::getModel('checkout/session')->getData('simirewardpoints_customerid'))->getGroupId()
//                    );
//                } else {
                $this->saveCache('abstract_customer_group_id', Mage::getSingleton('customer/session')->getCustomerGroupId());
//                }
            }
        }
        return $this->getCache('abstract_customer_group_id');
    }

    /**
     * get Website ID, depend on current checkout session (admin, frontend)
     * 
     * @return int
     */
    public function getWebsiteId() {
        if (!$this->hasCache('abstract_website_id')) {
            if (Mage::app()->getStore()->isAdmin()) {
                $this->saveCache('abstract_website_id', Mage::getSingleton('adminhtml/session_quote')->getStore()->getWebsiteId()
                );
            } else {
                $this->saveCache('abstract_website_id', Mage::app()->getStore()->getWebsiteId());
            }
        }
        return $this->getCache('abstract_website_id');
    }

}
