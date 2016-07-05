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
 * Simirewardpoints Spending Edit Tabs Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Adminhtml_Spending_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('simirewardpoints_spending_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simirewardpoints')->__('Spending Rate Information'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Simi_Simirewardpoints_Block_Adminhtml_Spending_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('simirewardpoints')->__('Spending Rate Information'),
            'title'     => Mage::helper('simirewardpoints')->__('Spending Rate Information'),
            'content'   => $this->getLayout()
                                ->createBlock('simirewardpoints/adminhtml_spending_edit_tab_form')
                                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}
