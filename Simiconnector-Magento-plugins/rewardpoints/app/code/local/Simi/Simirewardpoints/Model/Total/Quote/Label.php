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
 * Simirewardpoints Show Label Model
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Model_Total_Quote_Label extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('simirewardpoints_label');
    }
    
    /**
     * collect reward point label 
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Simi_Simirewardpoints_Model_Total_Quote_Label
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return $this;
        }
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }
        $address->setSimirewardpointsSpent(0);
        $address->setSimirewardpointsBaseDiscount(0);
        $address->setSimirewardpointsDiscount(0);
        $address->setSimirewardpointsEarn(0);
        $address->setSimiBaseDiscount(0);
        
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $child->setSimirewardpointsBaseDiscount(0)
                            ->setSimirewardpointsDiscount(0)
                            ->setSimiBaseDiscount(0)
                            ->setSimirewardpointsSpent(0);
                }
            } elseif ($item->getProduct()) {
                $item->setSimirewardpointsBaseDiscount(0)
                        ->setSimirewardpointsDiscount(0)
                        ->setSimiBaseDiscount(0)
                        ->setSimirewardpointsSpent(0);
            }
        }
        return $this;
    }
    /**
     * add point label
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Simi_Simirewardpoints_Model_Total_Quote_Label
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(array(
            'code'  => $this->getCode(),
            'title' => '1',
            'value' => 1,
        ));
        return $this;
    }
}
