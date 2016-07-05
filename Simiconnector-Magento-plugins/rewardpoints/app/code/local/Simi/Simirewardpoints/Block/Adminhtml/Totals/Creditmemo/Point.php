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
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Adminhtml_Totals_Creditmemo_Point
    extends Mage_Adminhtml_Block_Sales_Order_Totals_Item
{
    /**
     * add points value into creditmemo total
     *     
     */
    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $creditmemo = $totalsBlock->getCreditmemo();
        if ($creditmemo->getSimirewardpointsEarn()) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'simirewardpoints_earn_label',
                'label' => $this->__('Refund Points Earn'),
                'value' => $creditmemo->getSimirewardpointsEarn(),
                'is_formated'   => true,
            )), 'subtotal');
        }
        if ($creditmemo->getSimirewardpointsDiscount()>=0.0001) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'simirewardpoints',
                'label' => $this->__('Point Discount'),
                'value' => -$creditmemo->getSimirewardpointsDiscount(),
            )), 'subtotal');
        }
    }
}
