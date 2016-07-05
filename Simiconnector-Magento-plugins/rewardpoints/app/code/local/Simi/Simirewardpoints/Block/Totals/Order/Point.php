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
 * Simirewardpoints Total Point Spend Block
 * You should write block extended from this block when you write plugin
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Totals_Order_Point extends Simi_Simirewardpoints_Block_Template
{
    /**
     * add points value into order total
     *     
     */
    public function initTotals()
    {
        if (!$this->isEnable()) {
            return $this;
        }
        $totalsBlock = $this->getParentBlock();
        $order = $totalsBlock->getOrder();
        if ($order->getSimirewardpointsEarn()) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code' => 'simirewardpoints_earn_label',
                'label' => $this->__('Earn Points'),
                'value' => Mage::helper('simirewardpoints/point')->format($order->getSimirewardpointsEarn()),
                'base_value' => Mage::helper('simirewardpoints/point')->format($order->getSimirewardpointsEarn()),
                'is_formated' => true,
                    )), 'subtotal');
        }
        if ($order->getSimirewardpointsDiscount()>=0.0001) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'simirewardpoints',
                'label' => $this->__('Use points on spend'),
                'value' => -$order->getSimirewardpointsDiscount(),
                'base_value' => -$order->getSimirewardpointsBaseDiscount(),
            )), 'subtotal');
        }
    }
}
