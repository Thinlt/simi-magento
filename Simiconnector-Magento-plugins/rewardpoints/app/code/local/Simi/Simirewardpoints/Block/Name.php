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
 * Simirewardpoints Name and Image Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Name extends Simi_Simirewardpoints_Block_Template {

    /**
     * prepare block's layout
     *
     * @return Simi_Simirewardpoints_Block_Name
     */
    public function _prepareLayout() {
        $this->setTemplate('simirewardpoints/name.phtml');
        return parent::_prepareLayout();
    }

    /**
     * get current balance of customer as text
     * 
     * @return string
     */
    public function getBalanceText() {
        return Mage::helper('simirewardpoints/customer')->getBalanceFormated();
    }

    /**
     * get Image (Logo) HTML for reward points
     * 
     * @return string
     */
    public function getImageHtml() {
        return Mage::helper('simirewardpoints/point')->getImageHtml($this->getIsAnchorMode());
    }
}
