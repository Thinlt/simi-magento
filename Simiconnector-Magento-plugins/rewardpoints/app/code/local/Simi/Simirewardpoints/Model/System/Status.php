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
 * @copyright   Copyright (c) 2014 Simi (http://www.simi.com/)
 * @license     http://www.simi.com/license-agreement.html
 */

/**
 * Simirewardpoints Config Source Rounding Model
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */

class Simi_Simirewardpoints_Model_System_Status {
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1; 
    
    public function getOptionArray(){
        return array(
            self::STATUS_INACTIVE => Mage::helper('simirewardpoints')->__('Inactive'),
            self::STATUS_ACTIVE => Mage::helper('simirewardpoints')->__('Active'),
        );
    }
}
