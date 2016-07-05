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
 * Simirewardpoints Image Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Image extends Simi_Simirewardpoints_Block_Template
{
    protected $_rewardPointsHtml = null;
    protected $_rewardAnchorHtml = null;
    
    /**
     * prepare block's layout
     *
     * @return Simi_Simirewardpoints_Block_Image
     */
    public function _prepareLayout()
    {
        $this->setTemplate('simirewardpoints/image.phtml');
        return parent::_prepareLayout();
    }
    
    /**
     * Render block HTML, depend on mode of name showed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getIsAnchorMode()) {
            if (is_null($this->_rewardAnchorHtml)) {
                $html = parent::_toHtml();
                if ($html) {
                    $this->_rewardAnchorHtml = $html;
                } else {
                    $this->_rewardAnchorHtml = '';
                }
            }
            return $this->_rewardAnchorHtml;
        } else {
            if (is_null($this->_rewardPointsHtml)) {
                $html = parent::_toHtml();
                if ($html) {
                    $this->_rewardPointsHtml = $html;
                } else {
                    $this->_rewardPointsHtml = '';
                }
            }
            return $this->_rewardPointsHtml;
        }
    }
    
    /**
     * get Policy Link for reward points system
     * 
     * @return string
     */
    public function getPolicyUrl()
    {
        return Mage::helper('simirewardpoints/policy')->getPolicyUrl();
    }
}
