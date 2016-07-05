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
 * Simirewardpoints Rewrite Checkout Cart Coupon Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Checkout_Cart_Rewrite_Coupon
    extends Mage_Checkout_Block_Cart_Coupon
{
    /**
     * get current used coupon code
     * 
     * @return string
     */
    public function getCouponCode()
    {
        $container = new Varien_Object(array(
            'coupon_code'   => '',
        ));
        Mage::dispatchEvent('simirewardpoints_rewrite_coupon_block_get_coupon_code', array(
            'block'     => $this,
            'container' => $container,
        ));
        if ($container->getCouponCode()) {
            return $container->getCouponCode();
        }
        return parent::getCouponCode();
    }
    
     /**
     * render coupon block
     * 
     * @return html
     */
    protected function _toHtml()
    {
        $container = new Varien_Object(array(
            'html'          => '',
            'rewrite_core'  => false,
        ));
        Mage::dispatchEvent('simirewardpoints_rewrite_coupon_block_to_html', array(
            'block'     => $this,
            'container' => $container,
        ));
        if ($container->getRewriteCore() && $container->getHtml()) {
            return $container->getHtml();
        }
        if ($this->getChild('checkout.cart.simirewardpoints')) {
            $html=$this->getChildHtml('checkout.cart.simirewardpoints');
            $this->unsetChild('checkout.cart.simirewardpoints');
            return $container->getHtml()
                . $html
                . parent::_toHtml();
        }
        return $container->getHtml() . parent::_toHtml();
    }
}
