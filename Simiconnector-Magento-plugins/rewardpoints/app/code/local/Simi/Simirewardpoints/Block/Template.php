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
 * Simirewardpoints Core Block Template Block
 * You should write block extended from this block when you write plugin
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Template extends Mage_Core_Block_Template {

    /**
     * check reward points system is enabled or not
     * 
     * @return boolean
     */
    public function isEnable() {
        return Mage::helper('simirewardpoints')->isEnable();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml() {
        if ($this->isEnable()) {
            return parent::_toHtml();
        }
        return '';
    }

    public function isPluginEnable($plugin) {
        if (!$plugin) {
            return false;
        }
        if (Mage::helper('core')->isModuleEnabled($plugin) && Mage::helper('core')->isModuleOutputEnabled($plugin)) {
            return true;
        }
        return false;
    }

}
