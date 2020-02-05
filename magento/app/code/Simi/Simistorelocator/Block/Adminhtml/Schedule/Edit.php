<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Schedule;

class Edit extends \Magento\Backend\Block\Widget\Form\Container {

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     */
    protected function _construct() {
        $this->_objectId = 'schedule_id';
        $this->_blockGroup = 'Simi_Simistorelocator';
        $this->_controller = 'adminhtml_schedule';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Schedule'));
        $this->buttonList->update('delete', 'label', __('Delete'));

        $this->buttonList->add(
                'saveandcontinue', [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
            ],
                ], -100
        );

        $this->buttonList->add(
                'new-button', [
            'label' => __('Save and New'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndNew', 'target' => '#edit_form'],
                ],
            ],
                ], 10
        );

        $this->_formScripts[] = 'function toggleEditor() {
                if (tinyMCE.getInstanceById(\'schedule_content\') == null) {
                    tinyMCE.execCommand(\'mceAddControl\', false, \'schedule_content\');
                } else {
                    tinyMCE.execCommand(\'mceRemoveControl\', false, \'schedule_content\');
                }
            }

            require([\'simi/schedule\']);
          
                     
                        
                    require([
                            "jquery",
                            "underscore",
                            "mage/mage",
                            "mage/backend/tabs",
                            "domReady!"
                        ], function($) {
                       
                            var $form = $(\'#edit_form\');
                            $form.mage(\'form\', {
                                handlersData: {
                                    save: {},
                                    saveAndNew: {
                                        action: {
                                            args: {back: \'new\'}
                                        }
                                    },
                                }
                            });

                        });
        
         ';
    }

}
