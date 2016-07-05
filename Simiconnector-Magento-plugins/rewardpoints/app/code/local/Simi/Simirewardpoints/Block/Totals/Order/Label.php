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
 * Simirewardpoints Total Label Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Totals_Order_Label extends Simi_Simirewardpoints_Block_Template
{
    /**
     * add points label into creditmemo total
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
                'code'  => 'simirewardpoints_earn_label',
                'label' => $this->__('Earn Points'),
                'value' => Mage::helper('simirewardpoints/point')->format($order->getSimirewardpointsEarn()),
                'is_formated'   => true,
            )), 'first');
        }
        
        if ($order->getSimirewardpointsSpent()) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'simirewardpoints_spent_label',
                'label' => $this->__('Spend Points'),
                'value' => Mage::helper('simirewardpoints/point')->format($order->getSimirewardpointsSpent()),
                'is_formated'   => true,
            )), 'first');
        }
    }
}
