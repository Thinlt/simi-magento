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
 * Simirewardpoints Earning Edit Form Content Tab Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Adminhtml_Earning_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Simi_Simirewardpoints_Block_Adminhtml_Earning_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();


        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData();
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        } elseif (Mage::registry('rate_data')) {
            $data = Mage::registry('rate_data')->getData();
        }
        $fieldset = $form->addFieldset('simirewardpoints_form', array(
            'legend' => Mage::helper('simirewardpoints')->__('Rate Information')
        ));

        $dataObj = new Varien_Object($data);

        $data = $dataObj->getData();

        $fieldset->addField('money', 'text', array(
            'name' => 'money',
            'label' => Mage::helper('simirewardpoints')->__('Amount of money spent'),
            'title' => Mage::helper('simirewardpoints')->__('Amount of money spent per order'),
            'required' => true,
            'after_element_html' => '<strong>[' . Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE) . ']</strong>',
        ));

        $fieldset->addField('points', 'text', array(
            'label' => Mage::helper('simirewardpoints')->__('Earning Point(s)'),
            'title' => Mage::helper('simirewardpoints')->__('Earning Point(s)'),
            'required' => true,
            'name' => 'points',
            'note' => Mage::helper('simirewardpoints')->__('Example: When "Amount of money spent per order" is 10, "Earning Point(s)" is 1. If Customer spends $30, he will receive 3 points'),
        ));
        
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('simirewardpoints')->__('Status'),
            'title' => Mage::helper('simirewardpoints')->__('Status'),
            'required' => true,
            'name' => 'status',
            'options' => Mage::getSingleton('simirewardpoints/system_status')->getOptionArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name' => 'website_ids[]',
                'label' => Mage::helper('simirewardpoints')->__('Websites'),
                'title' => Mage::helper('simirewardpoints')->__('Websites'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray(),
            ));
        } else {
            $fieldset->addField('website_ids', 'hidden', array(
                'name' => 'website_ids[]',
                'value' => Mage::app()->getStore(true)->getWebsiteId()
            ));
            $data['website_ids'] = Mage::app()->getStore(true)->getWebsiteId();
        }

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'label' => Mage::helper('simirewardpoints')->__('Customer groups'),
            'title' => Mage::helper('simirewardpoints')->__('Customer groups'),
            'name' => 'customer_group_ids',
            'required' => true,
            'values' => Mage::getResourceModel('customer/group_collection')
                    ->toOptionArray()
        ));
        $fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('simirewardpoints')->__('Priority'),
            'label' => Mage::helper('simirewardpoints')->__('Priority'),
            'required' => false,
            'name' => 'sort_order',
            'note' => Mage::helper('simirewardpoints')->__('Higher priority Rate will be applied first'),
            'class' => 'validate-zero-or-greater'
        ));

        $form->setValues($data);
        $this->setForm($form);
        Mage::dispatchEvent('simirewardpoints_adminhtml_earning_rate_form', array(
            'tab_form' => $this,
            'form' => $form,
            'data' => $dataObj,
        ));
        return parent::_prepareForm();
    }

}
