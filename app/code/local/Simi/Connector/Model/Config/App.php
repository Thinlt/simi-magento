<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Connector
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Connector Model
 * 
 * @category    
 * @package     Connector
 * @author      Developer
 */
class Simi_Connector_Model_Config_App extends Simi_Connector_Model_Abstract {

    public function getCurrentStoreId() {
        return Mage::app()->getStore()->getId();
    }

    public function getConfigApp() {
        $country_code = Mage::getStoreConfig('general/country/default');
        $country = Mage::getModel('directory/country')->loadByCode($country_code);
        $locale = Mage::app()->getLocale()->getLocaleCode();
        $currencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        $currencySymbol = Mage::app()->getLocale()->currency($currencyCode)->getSymbol();
        $options = Mage::getResourceSingleton('customer/customer')->getAttribute('gender')->getSource()->getAllOptions();
        $values = array();
        foreach ($options as $option) {
            if ($option['value']) {
                $values[] = array(
                    'label' => $option['label'],
                    'value' => $option['value'],
                );
            }
        }

        //King RTL 5/7/2015
        $rtlCountry = Mage::getStoreConfig('connector/general/rtl_country', Mage::app()->getStore()->getId());
        $isRtl = '0';
        $rtlCountry = explode(',', $rtlCountry);
        if(in_array($country_code, $rtlCountry)){
            $isRtl = '1';
        } 
        //end King
        //King Giftcard
        $enableGiftCard = 0;
        $enableGiftCardCredit = 0;
        if(Mage::getConfig()->getModuleConfig('Magestore_Giftvoucher')->is('active', 'true')){
            $enableGiftCard = 1;
            if (Mage::helper('giftvoucher')->getGeneralConfig('enablecredit')){
                $enableGiftCardCredit = 1;
            }
        }
        $data = array(
            'store_config' => array(
                'country_code' => $country->getId(),
                'country_name' => $country->getName(),
                'locale_identifier' => $locale,
                'currency_symbol' => $currencySymbol,
                'currency_code' => $currencyCode,
				'currency_position' => $this->getCurrencyPosition(),
                'store_id' => $this->getCurrentStoreId(),
                'store_name' => Mage::app()->getStore()->getName(),
				'store_code' => Mage::app()->getStore()->getCode(),
				'is_show_zero_price' => Mage::getStoreConfig('connector/general/is_show_price_zero'),
				'is_show_link_all_product' => Mage::getStoreConfig('connector/general/is_show_all_product'),
				'use_store' => Mage::getStoreConfig('web/url/use_store'),
                // 'is_use_default_address' => Mage::getStoreConfig('connector/general/is_use_default_address'),
                'is_reload_payment_method' => Mage::getStoreConfig('connector/general/is_reload_payment_method'),
                'is_rtl' => $isRtl,
                'enable_giftcard' => $enableGiftCard,
                'enable_giftcard_credit' => $enableGiftCardCredit,
                'cometchat_link' => Mage::getStoreConfig('connector/general/cometchat_link'),
            ),
            'customer_address_config' => array(
                'prefix_show' => Mage::getStoreConfig('customer/address/prefix_show'),
                'suffix_show' => Mage::getStoreConfig('customer/address/suffix_show'),
                'dob_show' => Mage::getStoreConfig('customer/address/dob_show'),
                'taxvat_show' => Mage::getStoreConfig('customer/address/taxvat_show'),
                'gender_show' => Mage::getStoreConfig('customer/address/gender_show'),
                'gender_value' => $values,
            ),
            'checkout_config' => array(
                'enable_guest_checkout' => Mage::getStoreConfig('checkout/options/guest_checkout'),
                'enable_agreements' => is_null(Mage::getStoreConfig('checkout/options/enable_agreements')) ? 0 : Mage::getStoreConfig('checkout/options/enable_agreements'),
				'taxvat_show' => Mage::getStoreConfig('customer/create_account/vat_frontend_visibility'),
            ),
			'view_products_default'=>Mage::getStoreConfig('connector/general/show_product_type'),
            'android_sender' => Mage::getStoreConfig('connector/android_sendid'),
        );
        $information = $this->statusSuccess();
        $information['data'] = array($data);
        return $information;
    }

    public function getBannerList() {
        $list = Mage::getModel('connector/banner')->getBannerList();
        if (count($list)) {
            $information = $this->statusSuccess();
            $information['data'] = $list;
            return $information;
        } else {
            $information = $this->statusError();
            return $information;
        }
    }

    public function getMerchantInfo() {
        $website_id = Mage::app()->getWebsite()->getId();
        $listBlock = Mage::getModel('connector/cms')->getCollection()
                ->addFieldToFilter('website_id', array('in' => array($website_id, 0)))
                ->addFieldToFilter("cms_status", 1);
        $data = array();
		$helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();  
        foreach ($listBlock as $block) {
            $path = Mage::getBaseUrl('media') . 'simi/simicart/cms' . '/' . $block->getWebsiteId() . '/' . $block->getCmsImage();
            $data[] = array(
                "title" => $block->getCmsTitle(),
                "content" => $processor->filter($block->getCmsContent()),
                "icon" => $path,
            );
        }

        $information = $this->statusSuccess();
        $information['data'] = $data;
        return $information;
    }

    /**
     * 
     * @param type $data
     */
    public function saveConfigWebsite($logo_url, $web_id) {
        $logo = Mage::helper('connector')->getDirLogoImage($web_id);
        $url = $logo_url;
        file_put_contents($logo, file_get_contents($url));
    }

    public function getListPlugin($device_id) {
        $plugins = Mage::getModel('connector/plugin')->getListPlugin($device_id);
		$data = array();
        if ($plugins->getSize()) {            
            foreach ($plugins as $plugin) {
//                if ($this->checkPlugin($plugin->getPluginSku())) {
                    $data[] = array(
                        'name' => $plugin->getPluginName(),
                        'version' => $plugin->getPluginVersion(),
                        'sku' => $plugin->getPluginSku(),
                    );
//                }
            }
            
        }
		$information = $this->statusSuccess();
        $information['data'] = $data;
        return $information;
    }

    public function checkPlugin($sku_plugins) {
        $modules = Mage::getConfig()->getNode('modules')->children();
        foreach ($modules as $moduleName => $moduleInfo) {
            if (strcmp(strtolower($sku_plugins), strtolower($moduleName))) {
                if ($moduleInfo->active == true) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }
	
	public function getCurrencyPosition(){		
		$formated = Mage::app()->getStore()->getCurrentCurrency()->formatTxt(0);		
        $number = Mage::app()->getStore()->getCurrentCurrency()->formatTxt(0, array('display' => Zend_Currency::NO_SYMBOL));
		// Zend_debug::dump($number);
		 $ar_curreny = explode($number,$formated);
		if ($ar_curreny['0'] != ''){
			return 'before';
		}
		return 'after';
	}
}