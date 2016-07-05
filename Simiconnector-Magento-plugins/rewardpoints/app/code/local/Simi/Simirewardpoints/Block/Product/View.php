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
 * Rewrite Product View Page for Magento version 1.4 Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Product_View extends Simi_Simirewardpoints_Block_Template
{
    /**
     * prepare product info.extrahint block information
     *
     * @return Simi_Simirewardpoints_Block_Template
     */
    public function _prepareLayout()
    {
        if ($this->isEnable() && version_compare(Mage::getVersion(), '1.4.1.0', '<')) {
            $productInfo = $this->getLayout()->getBlock('product.info');
            $productInfo->setTemplate('simirewardpoints/product/view.phtml');
            $extrahints = $this->getLayout()->createBlock('core/text_list', 'product.info.extrahint');
            $productInfo->setChild('extrahint', $extrahints);
        }
        return parent::_prepareLayout();
    }
}
