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
 * Simirewardpoints Earning Grid Renderer Money Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Adminhtml_Earning_Renderer_Money
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($row->getDirection() == Simi_Simirewardpoints_Model_Rate::MONEY_TO_POINT) {
            return Mage::app()->getStore()->getBaseCurrency()->format($row->getMoney());
        } else {
            $result = new Varien_Object(array(
                'value' => ''
            ));
            Mage::dispatchEvent('simirewardpoints_adminhtml_earning_rate_grid_renderer', array(
                'row'   => $row,
                'result'=> $result
            ));
            return $result->getData('value');
        }
    }
}
