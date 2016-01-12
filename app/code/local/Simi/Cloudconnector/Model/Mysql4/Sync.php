<?php
/**
 * Simi
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Simi.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Simi
 * @package 	Simi_Cloudconnector
 * @copyright 	Copyright (c) 2012 Simi (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Madapter Resource Model
 * 
 * @category 	Simi
 * @package 	Simi_Cloudconnector
 * @author  	Simi Developer
 */
class Simi_Cloudconnector_Model_Mysql4_Sync extends Mage_Core_Model_Mysql4_Abstract
{
	/**
     * Internal constructor
     */
	public function _construct(){
		$this->_init('cloudconnector/sync', 'id');
	}
}