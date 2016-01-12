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
 * @category 	Magestore
 * @package 	Magestore_Cloudconnector
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Cloudconnector Helper
 *
 * @category 	Magestore
 * @package 	Magestore_Cloudconnector
 * @author  	Magestore Developer
 */
class Simi_Cloudconnector_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * convert customer address
     *
     * @param    array, customer
     * @return   array
     */
    public function removeUpdateRecord($recordId) {
    	if($recordId && $recordId > 0){
	        $record = Mage::getModel('cloudconnector/sync')->load($recordId);
	        try{
	        	$record->delete();
	        }catch(Exception $e){

	        }
	    }
    }

    /**
     * get config
     *
     * @param    string
     * @return   string
     */
    public function getConfig($config) {
        return Mage::getStoreConfig('cloudconnector/general/'.$config, Mage::app()->getWebsite()->getId());
    }

}