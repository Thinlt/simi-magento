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
 * Cloudconnector Status Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Cloudconnector
 * @author  	Magestore Developer
 */
class Simi_Cloudconnector_Model_Sync extends Simi_Cloudconnector_Model_Abstract
{		
	/**
     * Internal constructor
     */
	public function _construct() {
        parent::_construct();
        $this->_init('cloudconnector/sync');
    }
}