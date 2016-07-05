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
 * Simirewardpoints Customer Grid Serializer Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Adminhtml_Transaction_Edit_Tab_Customer_Serializer
    extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('simirewardpoints/transaction/customer/serializer.phtml');
        return $this;
    }
    
    /**
     * init serializer block, called from layout
     * 
     * @param string $gridName
     * @param string $hiddenInputName
     */
    public function initSerializerBlock($gridName, $hiddenInputName)
    {
        $grid = $this->getLayout()->getBlock($gridName);
        $this->setGridBlock($grid)
            ->setInputElementName($hiddenInputName);
    }
}
