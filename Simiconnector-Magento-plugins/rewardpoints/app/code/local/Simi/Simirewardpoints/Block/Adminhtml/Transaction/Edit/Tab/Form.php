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
 * Simirewardpoints Transaction Edit Form Content Tab Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Adminhtml_Transaction_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Simi_Simirewardpoints_Block_Adminhtml_Transaction_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getSimirewardpointsData()) {
            $model = new Varien_Object(Mage::getSingleton('adminhtml/session')->getSimirewardpointsData());
            Mage::getSingleton('adminhtml/session')->setSimirewardpointsData(null);
        } elseif (Mage::registry('transaction_data')) {
            $model = Mage::registry('transaction_data');
        }
        $fieldset = $form->addFieldset('simirewardpoints_form', array(
            'legend'=>Mage::helper('simirewardpoints')->__('Transaction Information')
        ));
        
        if ($model->getId()) {
            $fieldset->addField('title', 'note', array(
                'label'     => Mage::helper('simirewardpoints')->__('Transaction Title'),
                'text'      => $model->getTitleHtml(),
            ));
            $fieldset->addField('customer_email', 'note', array(
                        'label'     => Mage::helper('simirewardpoints')->__('Customer Email'),
                        'text'      => sprintf('<a target="_blank" href="%s">%s</a>',
                                $this->getUrl('adminhtml/customer/edit', array('id' => $model->getCustomerId())),
                                $model->getCustomerEmail()
                            ),
            ));
            
            Mage::dispatchEvent('simirewardpoints_transaction_view_detail', array(
                'fieldset' => $fieldset,'modeltransaction' => $model
            ));
            
            try {
                $actionLabel = $model->getActionInstance()->getActionLabel();
            } catch (Exception $e) {
                Mage::logException($e);
                $actionLabel = '';
            }
            $fieldset->addField('action', 'note', array(
                'label'     => Mage::helper('simirewardpoints')->__('Action'),
                'text'      => $actionLabel,
            ));
            
            $statusHash = $model->getStatusHash();
            $fieldset->addField('status', 'note', array(
                'label'     => Mage::helper('simirewardpoints')->__('Status'),
                'text'      => isset($statusHash[$model->getStatus()])
                    ? '<strong>' . $statusHash[$model->getStatus()] . '</strong>' : '',
            ));
            
            $fieldset->addField('point_amount', 'note', array(
                'label'     => Mage::helper('simirewardpoints')->__('Points'),
                'text'      => '<strong>' . Mage::helper('simirewardpoints/point')->format(
                        $model->getPointAmount(),
                        $model->getStoreId()
                    ) . '</strong>',
            ));
            
            $fieldset->addField('point_used', 'note', array(
                'label'     => Mage::helper('simirewardpoints')->__('Point Used'),
                'text'      => Mage::helper('simirewardpoints/point')->format(
                        $model->getPointUsed(),
                        $model->getStoreId()
                    ),
            ));
            
            $fieldset->addField('created_time', 'note', array(
                'label'     => Mage::helper('simirewardpoints')->__('Created time'),
                'text'      => $this->formatTime($model->getCreatedTime(),
                        Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
                        true
                    ),
            ));
            
            $fieldset->addField('updated_time', 'note', array(
                'label'     => Mage::helper('simirewardpoints')->__('Updated At'),
                'text'      => $this->formatTime($model->getUpdatedTime(),
                        Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
                        true
                    ),
            ));
            
            if ($model->getExpirationDate()) {
                $fieldset->addField('expiration_date', 'note', array(
                    'label'     => Mage::helper('simirewardpoints')->__('Expire On'),
                    'text'      => '<strong>' . $this->formatTime($model->getExpirationDate(),
                            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
                            true
                        ) . '</strong>',
                ));
            }
            
            $fieldset->addField('store_id', 'note', array(
                'label'     => Mage::helper('simirewardpoints')->__('Store View'),
                'text'      => Mage::app()->getStore($model->getStoreId())->getName(),
            ));
            
            return parent::_prepareForm();
        }
        
        $fieldset->addField('customer_email', 'text', array(
            'label'     => Mage::helper('simirewardpoints')->__('Customer'),
            'title'     => Mage::helper('simirewardpoints')->__('Customer'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'customer_email',
            'readonly'  => true,
            'after_element_html' => '</td><td class="label"><a href="javascript:showSelectCustomer()" title="'
                . Mage::helper('simirewardpoints')->__('Select') . '">'
                . Mage::helper('simirewardpoints')->__('Select') . '</a>'
                . '<script type="text/javascript">
                    function showSelectCustomer() {
                        new Ajax.Request("'
                    . $this->getUrl('*/*/customer',array('_current'=>true))
                    . '", {
                            parameters: {form_key: FORM_KEY, selected_customer_id: $("customer_id").value || 0},
                            evalScripts: true,
                            onSuccess: function(transport) {
                                TINY.box.show("");
                                $("tinycontent").update(transport.responseText);
                            }
                        });
                    }
                </script>'
        ));
        
        $fieldset->addField('customer_id', 'hidden', array('name'  => 'customer_id'));
        
        $fieldset->addField('point_amount', 'text', array(
            'label'     => Mage::helper('simirewardpoints')->__('Points'),
            'title'     => Mage::helper('simirewardpoints')->__('Points'),
            'name'      => 'point_amount',
            'required'  => true,
        ));
        
        $fieldset->addField('title', 'textarea', array(
            'label'     => Mage::helper('simirewardpoints')->__('Transaction Title'),
            'title'     => Mage::helper('simirewardpoints')->__('Transaction Title'),
            'name'      => 'title',
            'style'     => 'height: 5em;'
        ));
        
        $fieldset->addField('expiration_day', 'text', array(
            'label'     => Mage::helper('simirewardpoints')->__('Points expire after'),
            'title'     => Mage::helper('simirewardpoints')->__('Points expire after'),
            'name'      => 'expiration_day',
            'note'      => Mage::helper('simirewardpoints')->__('day(s) since the transaction date. If empty or zero, there is no limitation.')
        ));
        
        $form->setValues($model->getData());
        return parent::_prepareForm();
    }
}
