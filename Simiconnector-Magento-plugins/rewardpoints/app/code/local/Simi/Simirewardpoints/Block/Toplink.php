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
 * Simirewardpoints Update Top Link Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Toplink extends Simi_Simirewardpoints_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Simi_Simirewardpoints_Block_Name
     */
    public function _prepareLayout()
    {
        $helper = Mage::helper('simirewardpoints/customer');
        if (!Mage::getStoreConfig('advanced/modules_disable_output/Simi_Simirewardpoints')
            && $this->isEnable() && $helper->getCustomerId() && $helper->showOnToplink()
        ) {
            $block = $this->getLayout()->getBlock('top.links');
            
            $accountUrl  = Mage::helper('customer')->getAccountUrl();
            $nameBlock = Mage::getBlockSingleton('simirewardpoints/name');
            if(is_object($block)){
		$block->removeLinkByUrl($accountUrl);
		$block->addLink(
                    $this->__('My Account') . ' (' . $nameBlock->toHtml() . ')',
                    $accountUrl,
                    $this->__('My Account'),
                    '', '', 10
		);
            }
        }
        
        return parent::_prepareLayout();
    }
    
    /**
     * functional block - using to change other block information
     * 
     * @return string
     */
    protected function _toHtml()
    {
        return '';
    }
}
