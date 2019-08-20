<?php

namespace Vnecoms\PdfProCustomVariables\Block\Adminhtml\Variables;

/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 23:28
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'custom_variable_id';
        $this->_controller = 'adminhtml_variables';
        $this->_blockGroup = 'Vnecoms_PdfProCustomVariables';

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );
    }

    /**
     * Get global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('pdfprocustomvariables_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'pdfprocustomvariables_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'pdfprocustomvariables_content');
                }
            }
            
            require([
                'jquery',   
                'domReady!'
            ], function(jQuery){
            
                function saveAndContinueEdit(){
                    editForm.submit(jQuery('edit_form').action+'back/edit/');
                }
        		
                function toggleActionsSelect(action) {
                    if(action == 'attribute') {                    
                        jQuery(\".field-static_value\").hide();
                       
                    }else if(action == 'static') {
                        console.log('show field static');
                        jQuery(\".field-static_value\").show();
                        
                    }else if(action == 'customer') {
                        jQuery(\".field-static_value\").hide();
                        
                    }	
                }
        		
        		toggleActionsSelect(jQuery(\"#variable_variable_type\").val());
            }); 
        ";
        return parent::_prepareLayout();
    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $variable = $this->coreRegistry->registry('current_variable');
        if ($variable->getCustomVariableId()) {
            return __("Edit Variable '%1'", $this->escapeHtml($variable->getName()));
        } else {
            return __('New Variable');
        }
    }
}
