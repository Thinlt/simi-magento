<?php
/**
 * Vendor Profile Process Edit Block
 */
namespace Vnecoms\VendorsProfileNotification\Block\Adminhtml\Process;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

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
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Apply" button
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Vnecoms_VendorsProfileNotification';
        $this->_controller = 'adminhtml_process';
        
        parent::_construct();
        $this->updateButton('save', 'label', __('Save Process'));

        $app = $this->_coreRegistry->registry('current_process');
        
           
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
        
        $this->_formScripts[] = "
            require([
            'jquery'
        ], function($){
            function switchProcessType(){
                $('.process_type_field').removeClass('required-entry');
                $('.process_type_field').parent().parent().hide();
                $('.'+$('#process_type').val()).parent().parent().show();
                $('.'+$('#process_type').val()).addClass('required-entry');
            }
            switchProcessType();
            $('#process_type').change(function(){
                switchProcessType();
            });
        });
            
        ";
        return $this;
    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $process = $this->_coreRegistry->registry('current_process');
        if ($process->getProcessId()) {
            return __("Edit Process '%s'", $this->escapeHtml($process->getTitle()));
        } else {
            return __('New Process');
        }
    }
}
